<?php

namespace Controllers;

class LoginController extends Controller {
    function beforeRoute() {
        //Overridden to prevent endless redirects if a user is redirected to the login page for not being logged in

        if ($this->f3->get("SESSION.user") !== null ) {
            if ($this->f3->get("ALIAS") == "loginPage") {
                $this->f3->reroute("@devices");
            }
        }
    }

    public function index()
    {
        $template = new \Template;
        echo $template->render("index.html");
    }

    function login($f3) {
        parse_str($_SERVER["QUERY_STRING"]);

        //Verify that the access token belongs to us
        $c = curl_init("https://api.amazon.com/auth/o2/tokeninfo?access_token=" . urlencode($access_token));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            
        $r = curl_exec($c);
        curl_close($c);
        $d = json_decode($r);
            
        if ($d->aud != $f3->get("AMAZON_TOKEN")) {
            //The access token does not belong to us
            header('HTTP/1.1 404 Not Found');
            echo 'Page not found';
            exit;
        }
            
        //Exchange the access token for user profile
        $c = curl_init("https://api.amazon.com/user/profile");
        curl_setopt($c, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $access_token));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            
        $r = curl_exec($c);
        curl_close($c);
        $d = json_decode($r);

        $db = $this->db;

        $userModel = new \Models\UserModel($db);

        $currentUser = $userModel->findUser($d->user_id)[0];

        if ($currentUser === NULL) {
            $userModel->createNewUser($d->name, $d->email, $d->user_id);
            $currentUser = $userModel->findUser($d->user_id)[0];
        }

        $f3->set("SESSION.user", $currentUser->UserID);

        $f3->reroute("@devices");
    }

    function logout($f3) {
        $f3->clear("SESSION.user");
        $f3->reroute("@loginPage");
    }
}