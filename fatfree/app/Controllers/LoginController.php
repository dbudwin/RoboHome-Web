<?php

namespace Controllers;

class LoginController extends Controller
{
    public function beforeRoute()
    {
        $this->redirectLoggedInUserToDevicesPage($this->f3);
    }

    public function index()
    {
        $template = new \Template;
        echo $template->render("index.html");
    }

    public function login($f3)
    {
        parse_str($_SERVER["QUERY_STRING"]);

        if ($this->verifyUserTokenMatchesAmazonToken($f3, $access_token)) {
            $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($access_token);
            $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

            $f3->set("SESSION.user", $loggedInUser->UserID);
            $f3->reroute("@devices");
        }

        $f3->error(401);
    }

    public function logout($f3)
    {
        $f3->clear("SESSION.user");
        $f3->reroute("@loginPage");
    }

    private function redirectLoggedInUserToDevicesPage($f3)
    {
        if (!$f3->devoid("SESSION.user")) {
            if ($f3->get("ALIAS") === "loginPage") {
                $f3->reroute("@devices");
            }
        }
    }

    private function verifyUserTokenMatchesAmazonToken($f3, $accessToken)
    {
        $amazonOAuthUrl = "https://api.amazon.com/auth/o2/tokeninfo?access_token=" . urlencode($accessToken);
        $amazonUserProfileCurlHandle = curl_init($amazonOAuthUrl);
        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_RETURNTRANSFER, true);

        $amazonUserProfileCurlResultJson = curl_exec($amazonUserProfileCurlHandle);
        curl_close($amazonUserProfileCurlHandle);
        $decodedUser = json_decode($amazonUserProfileCurlResultJson);

        $userToken = $decodedUser->aud;
        
        if ($userToken != $f3->get("AMAZON_TOKEN")) {
            return false;
        }

        return true;
    }

    private function exchangeAccessTokenForDecodedUserProfile($accessToken)
    {
        $amazonUserProfileCurlHandle = curl_init("https://api.amazon.com/user/profile");
        $httpHeaders = array("Authorization: bearer " . $accessToken);

        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($amazonUserProfileCurlHandle, CURLOPT_RETURNTRANSFER, true);

        $amazonUserProfileCurlResultJson = curl_exec($amazonUserProfileCurlHandle);
        curl_close($amazonUserProfileCurlHandle);

        $decodedUserProfileArray = json_decode($amazonUserProfileCurlResultJson);

        return $decodedUserProfileArray;
    }

    private function getLoggedInUserProfile($decodedUserProfile)
    {
        $db = $this->db;

        $userModel = new \Models\UserModel($db);

        $userId = $decodedUserProfile->user_id;

        $loggedInUser = $userModel->findUser($userId)[0];

        if ($loggedInUser === null) {
            $loggedInUser = $this->createNewUser($userModel, $decodedUserProfile);
        }

        return $loggedInUser;
    }

    private function createNewUser($userModel, $decodedUserProfile)
    {
        $name = $decodedUserProfile->name;
        $email = $decodedUserProfile->email;
        $userId = $decodedUserProfile->user_id;

        $userModel->add($name, $email, $userId);

        $newlyCreatedUser = $userModel->findUser($userId)[0];

        return $newlyCreatedUser;
    }
}
