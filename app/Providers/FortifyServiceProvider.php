<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Ogrenci;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Eski hash'li şifreler için özel login doğrulama
        Fortify::authenticateUsing(function (Request $request) {
            $user = Ogrenci::where('email', $request->email)->first();

            if ($user) {
                $hashed = $user->password;

                if (Str::startsWith($hashed, '$2y$')) {
                    if (Hash::check($request->password, $hashed)) {
                        return $user;
                    }
                } else {
                    $ci3Hash = sha1(md5($request->password));
                    if ($ci3Hash === $hashed) {
                        $user->password = Hash::make($request->password);
                        $user->save();
                        return $user;
                    }
                }
            }

            return null;
        });


        // Login işlemi
        Fortify::loginView(function () {
            return view(theme_view('ogrenci', 'auth.login'));
        });

        // Register işlemi
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Şifre sıfırlama işlemi
        Fortify::resetPasswordView(function () {
            return view('auth.password-reset');
        });


        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
