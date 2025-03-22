<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan model User sesuai dengan yang digunakan

class AppServiceProvider extends ServiceProvider
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
        Filament::serving(function () {
            // Cek jika user belum login
            if (!Auth::check()) {
                // Ambil user berdasarkan email yang sudah ditentukan
                $user = User::where('email', 'user@user.com')->first();
                
                if ($user) {
                    Auth::login($user); // Login otomatis
                }
            }
        });
    }
}
