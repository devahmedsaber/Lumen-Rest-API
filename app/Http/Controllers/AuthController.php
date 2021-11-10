<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiTrait;

    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        // Check Field is Not Empty
        if (empty($email) || empty($password))
            return $this->fail('You Must Fill All Fields.');

        $client = new Client();

        try {
            return $client->post(config('service.passport.login_endpoint'), [
                "form_params" => [
                    "client_secret" => config('service.passport.client_secret'),
                    "grant_type" => "password",
                    "client_id" => config('service.passport.client_id'),
                    "username" => $request->email,
                    "password" => $request->password
                ]
            ]);
        } catch (BadResponseException $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        // Check If Fields is Not Empty
        if (empty($name) || empty($email) || empty($password))
            return $this->fail('You Must Fill All Fields.');

        // Check If Email is Valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return $this->fail('You Must Enter Valid Email.');

        // Check If Password is Not Less Than 8 Character
        if (strlen($password) < 8)
            return $this->fail('Password Should Be Min 8 Characters.');

        // Check is User Already Exist
        if (User::where('email', $email)->exists())
            return $this->fail('User Already Exists With This Email.');

        // Create New User
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password)
            ]);

            if ($user)
                return $this->login($request);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::user()->tokens()->each(function ($token) {
                $token->delete();
            });

            return $this->success('Logged Out Successfully.');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
