<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view(theme_view('admin', 'pages.auth.login'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $hashed = $user->password;

            if (Str::startsWith($hashed, '$2y$')) {
                if (Hash::check($request->password, $hashed)) {
                    Auth::login($user, $request->filled('remember'));
                    return redirect()->intended(route(config('system.admin_prefix').'.dashboard'));
                }
            } else {
                $ci3Hash = sha1(md5($request->password));
                if ($ci3Hash === $hashed) {
                    $user->password = Hash::make($request->password);
                    $user->save();

                    Auth::login($user, $request->filled('remember'));
                    return redirect()->intended(route(config('system.admin_prefix').'.dashboard'));
                }
            }
        }

        return back()->withErrors(['email' => 'Bilgileriniz hatalı.'])->withInput();
    }



    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('auth.login');
    }
}
