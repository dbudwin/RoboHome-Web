<?php

namespace Web\Controllers;

class LoginController extends \Common\Controllers\AmazonLoginController
{
    public function beforeRoute()
    {
        $this->redirectLoggedInUserToDevicesPage($this->f3);
    }

    public function index()
    {
        $template = new \Template;
        echo $template->render('index.html');
    }

    public function login($f3)
    {
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

        parse_str($queryString, $queryStringArray);

        if (array_key_exists('access_token', $queryStringArray)) {
            $accessToken = $queryStringArray['access_token'];

            if ($this->verifyUserTokenMatchesAmazonToken($f3, $accessToken)) {
                $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($accessToken);
                $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

                $f3->set('SESSION.user', $loggedInUser->UserID);
                $f3->reroute('@devices');
            }
        }

        $f3->error(401);
    }

    public function logout($f3)
    {
        $f3->clear('SESSION.user');
        $f3->reroute('@loginPage');
    }

    private function redirectLoggedInUserToDevicesPage($f3)
    {
        if (!$f3->devoid('SESSION.user')) {
            if ($f3->get('ALIAS') === 'loginPage') {
                $f3->reroute('@devices');
            }
        }
    }
}
