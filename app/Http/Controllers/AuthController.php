<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiTrait;
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

        // Check If Field is Empty
        if (empty($email) || empty($password)) {
            return $this->fail('You Must Fill All The Fields.');
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
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

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            Auth::logout();

            return $this->success('Logged Out Successfully.');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
