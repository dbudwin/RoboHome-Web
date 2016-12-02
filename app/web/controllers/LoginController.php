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
        parse_str($_SERVER['QUERY_STRING']);

        if ($this->verifyUserTokenMatchesAmazonToken($f3, $access_token)) {
            $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($access_token);
            $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

            $f3->set('SESSION.user', $loggedInUser->UserID);
            $f3->reroute('@devices');
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
