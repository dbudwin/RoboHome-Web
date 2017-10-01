<?php

namespace App\Http\Authentication;

use App\Http\Wrappers\ICurlRequest;
use App\User;
use Illuminate\Http\Request;

class AmazonLoginAuthenticator implements ILoginAuthenticator
{
    private $curlRequest;
    private $userModel;

    public function __construct(ICurlRequest $curlRequest, User $userModel)
    {
        $this->curlRequest = $curlRequest;
        $this->userModel = $userModel;
    }

    public function processLoginRequest(Request $request): ?User
    {
        $accessToken = $request->query('access_token');

        $loggedInUser = $this->getUserForAccessToken($accessToken);

        return $loggedInUser;
    }

    public function processApiLoginRequest(Request $request): ?User
    {
        $httpAuthorizationHeader = $request->header('Authorization');

        preg_match('/Bearer (.*)/', $httpAuthorizationHeader, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $accessToken = $matches[1];

        $loggedInUser = $this->getUserForAccessToken($accessToken);

        return $loggedInUser;
    }

    private function getUserForAccessToken(?string $accessToken): ?User
    {
        if (empty($accessToken) || !$this->verifyUserTokenMatchesAmazonToken($accessToken)) {
            return null;
        }

        $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($accessToken);
        $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

        if ($loggedInUser === null) {
            $loggedInUser = $this->createNewUserIfUserDoesNotExist($decodedUserProfile);
        }

        return $loggedInUser;
    }

    private function verifyUserTokenMatchesAmazonToken(string $accessToken): bool
    {
        $amazonOAuthUrl = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($accessToken);
        $this->curlRequest->init($amazonOAuthUrl);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        $amazonUserAccessTokenResultJson = $this->curlRequest->execute();
        $this->curlRequest->close();
        $decodedUserAccessTokenArray = json_decode($amazonUserAccessTokenResultJson);

        if (!isset($decodedUserAccessTokenArray->aud)) {
            return false;
        }

        $userToken = $decodedUserAccessTokenArray->aud;

        if ($userToken != env('AMAZON_TOKEN')) {
            return false;
        }

        return true;
    }

    private function exchangeAccessTokenForDecodedUserProfile(string $accessToken): \stdClass
    {
        $this->curlRequest->init('https://api.amazon.com/user/profile');
        $httpHeaders = ['Authorization: bearer ' . $accessToken];
        $this->curlRequest->setOption(CURLOPT_HTTPHEADER, $httpHeaders);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        $amazonUserProfileCurlResultJson = $this->curlRequest->execute();
        $this->curlRequest->close();
        $decodedUserProfileArray = json_decode($amazonUserProfileCurlResultJson);

        return $decodedUserProfileArray;
    }

    private function getLoggedInUserProfile($decodedUserProfile): ?User
    {
        $userId = $decodedUserProfile->user_id;
        $loggedInUser = $this->userModel->where('user_id', $userId)->first();

        return $loggedInUser;
    }

    private function createNewUserIfUserDoesNotExist($decodedUserProfile): User
    {
        $user = $this->userModel->add(
            $decodedUserProfile->name,
            $decodedUserProfile->email,
            $decodedUserProfile->user_id
        );

        return $user;
    }
}
