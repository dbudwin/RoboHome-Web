<?php

namespace App\Http\Controllers\Web;

use App\Http\Authentication\ILoginAuthenticator;
use App\Http\Controllers\Web\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    private $loginAuthenticator;

    public function __construct(ILoginAuthenticator $loginAuthenticator)
    {
        $this->loginAuthenticator = $loginAuthenticator;
    }

    public function index(): View
    {
        if (session()->has(env('SESSION_USER_ID'))) {
            return $this->devices();
        }

        return view('index');
    }

    public function login(Request $request): RedirectResponse
    {
        $loggedInUser = $this->loginAuthenticator->processLoginRequest($request);

        if ($loggedInUser === null) {
            abort(401, 'Unauthorized');
        }

        session([env('SESSION_USER_ID') => $loggedInUser->user_id]);

        return redirect()->route('devices');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();

        return redirect()->route('index');
    }
}
