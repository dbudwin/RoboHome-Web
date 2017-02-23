<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Common\Controller;
use App\Http\Wrappers\ICurlRequest;
use App\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $userModel;
    private $curlRequest;

    public function __construct(User $userModel, ICurlRequest $curlRequest)
    {
        $this->userModel = $userModel;
        $this->curlRequest = $curlRequest;
    }

    public function index()
    {
        if (session()->has(env('SESSION_USER_ID'))) {
            return redirect()->route('devices');
        }

        return view('index');
    }

    public function login(Request $request)
    {
        $accessToken = $request->query('access_token');

        if (empty($accessToken) || !$this->verifyUserTokenMatchesAmazonToken($accessToken)) {
            abort(401, 'Access token not returned from Amazon.');
        }

        $decodedUserProfile = $this->exchangeAccessTokenForDecodedUserProfile($accessToken);
        $loggedInUser = $this->getLoggedInUserProfile($decodedUserProfile);

        session([env('SESSION_USER_ID') => $loggedInUser->user_id]);

        return redirect()->route('devices');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('index');
    }

    private function verifyUserTokenMatchesAmazonToken($accessToken)
    {
        $amazonOAuthUrl = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($accessToken);
        $this->curlRequest->init($amazonOAuthUrl);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        $amazonUserAccessTokenResultJson = $this->curlRequest->execute();
        $this->curlRequest->close();
        $decodedUserAccessTokenArray = json_decode($amazonUserAccessTokenResultJson);
        $userToken = $decodedUserAccessTokenArray->aud;

        if ($userToken === null || $userToken != env('AMAZON_TOKEN')) {
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
