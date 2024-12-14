<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $google = Socialite::driver('google')->user();
            $se_registro = User::where('email', $google->email)->where('is_active', '!=', 0)->first();
            if (empty($se_registro)) {
                $isUDHEmail = str_ends_with($google->email, '@udh.edu.pe');
                if (!$isUDHEmail) {
                    throw new \Exception('Se requiere un correo institucional de la UDH.');
                }
                $user = new User();
                $user->nombres = $google->user['given_name'];
                $user->apellidos = $google->user['family_name'];
                $user->email = $google->email;
                $user->password = bcrypt($this->usuarioCorreo($google->email));
                $user->email_verified_at = time();
                if ($this->esEstudianteUDH($google->email)) {
                    $user->codigo = $this->usuarioCorreo($google->email);
                }
                $user->save();
                Auth::login($user);
                return redirect()->intended(route('dashboard'));
            } else {
                if ($se_registro->is_active != 1) {
                    throw new \Exception('Su cuenta se encuentra suspendida.');
                }
                Auth::login($se_registro);
                return redirect()->intended(route('dashboard'));
            }
        } catch (\Exception $e) {
            return redirect('login')
                ->withErrors(['google' => $e->getMessage()]);
        }
    }

    private function usuarioCorreo($correo)
    {
        return strtok($correo, '@');
    }

    private function esEstudianteUDH($correo)
    {
        if (preg_match('/^\d+@udh\.edu\.pe$/', $correo)) {
            return true;
        } else {
            return false;
        }
    }
}
