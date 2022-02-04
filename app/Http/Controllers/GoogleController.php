<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $socialite = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $socialite['email'])->first();
            if ($user) {
                $user->update([
                    'google_id' => $socialite['id']
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'token' => $this->jwt($user)
                ]);
            }

            $user = User::create([
                'nama' => $socialite['name'],
                'email' => $socialite['email'],
                'telp' => '',
                'password' => '',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'google_id' => $socialite['id']
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'token' => $this->jwt($user)
            ]);
        } catch (InvalidStateException $e) {
            return $e->getMessage();
        }
    }

    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
