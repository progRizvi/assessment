<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function store(UserRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = $this->user->create($input);

        return response()->json($user, 201);

    }
    public function login(Request $request)
    {
        $user = $this->getUserByEmail($request->input('email'));

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }
        // generate a token
        $token = $this->generateCustomToken();

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    private function getUserByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }
    
    private function generateCustomToken()
    {
        return bin2hex(random_bytes(32));
    }

}
