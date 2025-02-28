<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Solicitar el scope de calendar.events
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events'
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent'
            ])
            ->redirect();
    }

    public function callback()
    {
        try {
            $google = Socialite::driver('google')->user();

            // Construir el token completo con toda la información relevante
            $tokenData = [
                'access_token'  => $google->token,
                'refresh_token' => $google->refreshToken,
                'expires_in'    => $google->expiresIn,
                'token_type'    => 'Bearer',
                'created'       => time(),
            ];

            $se_registro = User::where('email', $google->email)
                ->where('is_active', '!=', 0)
                ->first();

            if (empty($se_registro)) {
                $isUDHEmail = str_ends_with($google->email, '@udh.edu.pe');
                if (!$isUDHEmail) {
                    throw new \Exception('Se requiere un correo institucional de la UDH.');
                }
                $user = new User();
                $user->nombres = $google->user['given_name'] ?? null;
                $user->apellidos = $google->user['family_name'] ?? null;
                $user->email = $google->email;
                $user->password = bcrypt($this->usuarioCorreo($google->email));
                $user->email_verified_at = now();
                if ($this->esEstudianteUDH($google->email)) {
                    $user->codigo = $this->usuarioCorreo($google->email);
                }

                // Guardar el JSON completo de Google en la columna google_token_json
                $user->google_token_json = json_encode($tokenData);

                $user->save();
                Auth::login($user);
                return redirect()->intended(route('dashboard'));
            } else {
                if ($se_registro->is_active != 1) {
                    throw new \Exception('Su cuenta se encuentra suspendida.');
                }

                // Actualizar el token JSON de Google si cambia
                $se_registro->update([
                    'google_token_json' => json_encode($tokenData)
                ]);

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
        return preg_match('/^\d+@udh\.edu\.pe$/', $correo);
    }
}
