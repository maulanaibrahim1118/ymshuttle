<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return '/home';
    }

    /**
     * Set the login field.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Handle a failed login attempt.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $username = $request->input($this->username());

        LogActivity::log('user-login', 'User login failed', 'Invalid username or password.', $username);

        return redirect()->route('login')
            ->withInput($request->only($this->username(), 'remember'))
            ->with('error', 'Invalid username or password.');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $username = $request->input($this->username());
        $user = User::where($this->username(), $username)->first();

        // Cek jika user ditemukan tapi akun tidak aktif
        if ($user && $user->is_active == 0) {
            LogActivity::log('user-login', 'User login failed', 'Your account is inactive. Please contact administrator.', $user->username);

            return redirect()->route('login')
                ->withInput($request->only($this->username(), 'remember'))
                ->with('error', 'Your account is inactive. Please contact administrator.');
        }

        // Proses login jika kredensial cocok
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Gagal login (kredensial salah)
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log and handle a successful login response.
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        LogActivity::log('user-login', 'User login successfully', '', Auth::user()->username);

        $request->session()->flash('loginSuccess', 'Welcome back to the YMShuttle App!');

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}