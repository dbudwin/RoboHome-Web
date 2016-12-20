<?php

namespace API\Controllers;

class LoginController extends \Common\Controllers\AmazonLoginController
{
    public function validateUser($httpAuthorizationHeader)
    {
        preg_match('/Bearer (.*)/', $httpAuthorizationHeader, $matches);

        $accessToken = $matches[1];

        if ($this->verifyUserTokenMatchesAmazonToken($this->f3, $accessToken)) {
            $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($accessToken);
            $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

            return $loggedInUser->ID;
        }

        $this->f3->error(401);
    }
}
