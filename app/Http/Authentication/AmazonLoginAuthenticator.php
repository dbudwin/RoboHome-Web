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

    public function processLoginRequest(Request $request)
    {
        $accessToken = $request->query('access_token');

        if (empty($accessToken) || !$this->verifyUserTokenMatchesAmazonToken($accessToken)) {
            return null;
        }

        $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($accessToken);
        $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

        return $loggedInUser;
    }

    private function verifyUserTokenMatchesAmazonToken($accessToken)
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

    private function exchangeAccessTokenForDecodedUserProfile($accessToken)
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

    private function getLoggedInUserProfile($decodedUserProfile)
    {
        $userId = $decodedUserProfile->user_id;
        $loggedInUser = $this->userModel->where('user_id', $userId)->first();

        if ($loggedInUser === null) {
            $loggedInUser = $this->userModel->add(
                $decodedUserProfile->name,
                $decodedUserProfile->email,
                $decodedUserProfile->user_id
            );
        }

        return $loggedInUser;
    }
}
