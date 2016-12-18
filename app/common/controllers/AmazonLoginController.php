<?php

namespace Common\Controllers;

class AmazonLoginController extends Controller
{
    protected function verifyUserTokenMatchesAmazonToken($f3, $accessToken)
    {
        $amazonOAuthUrl = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($accessToken);
        $amazonUserProfileCurlHandle = curl_init($amazonOAuthUrl);
        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_RETURNTRANSFER, true);

        $amazonUserProfileCurlResultJson = curl_exec($amazonUserProfileCurlHandle);
        curl_close($amazonUserProfileCurlHandle);
        $decodedUser = json_decode($amazonUserProfileCurlResultJson);

        $userToken = $decodedUser->aud;

        if ($userToken === null || $userToken != $f3->get('AMAZON_TOKEN')) {
            return false;
        }

        return true;
    }

    protected function exchangeAccessTokenForDecodedUserProfile($accessToken)
    {
        $amazonUserProfileCurlHandle = curl_init('https://api.amazon.com/user/profile');
        $httpHeaders = array('Authorization: bearer ' . $accessToken);

        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_RETURNTRANSFER, true);

        $amazonUserProfileCurlResultJson = curl_exec($amazonUserProfileCurlHandle);
        curl_close($amazonUserProfileCurlHandle);

        $decodedUserProfileArray = json_decode($amazonUserProfileCurlResultJson);

        return $decodedUserProfileArray;
    }

    protected function getLoggedInUserProfile($decodedUserProfile)
    {
        $userModel = $this->container->get('UserModel');

        $userId = $decodedUserProfile->user_id;

        $loggedInUser = $userModel->findUser($userId)[0];

        if ($loggedInUser === null) {
            $loggedInUser = $this->createNewUser($userModel, $decodedUserProfile);
        }

        return $loggedInUser;
    }

    protected function createNewUser($userModel, $decodedUserProfile)
    {
        $name = $decodedUserProfile->name;
        $email = $decodedUserProfile->email;
        $userId = $decodedUserProfile->user_id;

        $userModel->add($name, $email, $userId);

        $newlyCreatedUser = $userModel->findUser($userId)[0];

        return $newlyCreatedUser;
    }
}
