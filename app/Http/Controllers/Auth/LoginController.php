<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Access\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {
    protected $redirectTo;
    protected $user;

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {
        return view('auth.login');
    }

    public function logMeIn(Request $request) {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $request->session()->put('auth.password_confirmed_at', time());
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);

            if ($response = $this->authenticated($request, $this->guard()->user())) {
                return $response;
            }
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request) {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request) {
        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['is_active' => true]);
    }

    /**
     * @throws \Exception
     */
    protected function sendLoginResponse(Request $request) {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        smilify('success', 'Login successfully');
        return redirect($this->redirectTo());
    }

    protected function authenticated(Request $request, $user) {
        //
    }

    protected function sendFailedLoginResponse(Request $request) {
        $credentials = $this->credentials($request);
        $user = User::where($this->username(), $credentials[$this->username()])->first();

        if ($user) {
            if (!\Hash::check($credentials['password'], $user->password)) {
                notify()->error('These credentials do not match our records.');
                return redirect()->back();
            }

            if (!$user->is_active) {
                notify()->error('Your account is blocked. Please contact the administrator.');
                return redirect()->back();
            }
        }
        notify()->error('These credentials do not match our records.');
        return redirect()->back();
    }

    public function username() {
        return 'email';
    }

    protected function guard() {
        return Auth::guard();
    }

    public function logout(Request $request) {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    protected function loggedOut(Request $request) {
        //
    }

    protected function redirectTo() {
        $user = user();
        if ($user) {
            $this->redirectTo = 'home';
        } else {
            $this->redirectTo = 'login';
        }
        return route($this->redirectTo);
    }

    private function clearLoginAttempts(Request $request) {
        RateLimiter::clear($this->throttleKey($request));
    }

    private function incrementLoginAttempts(Request $request) {
        RateLimiter::hit($this->throttleKey($request));
    }

    private function fireLockoutEvent(Request $request) {
        event(new \Illuminate\Auth\Events\Lockout($request));
    }

    private function sendLockoutResponse(Request $request) {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.throttle', ['seconds' => RateLimiter::availableIn($this->throttleKey($request))])],
        ])->status(429);
    }

    private function throttleKey(Request $request) {
        return strtolower($request->input($this->username())) . '|' . $request->ip();
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), 5);
    }

    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }
}
