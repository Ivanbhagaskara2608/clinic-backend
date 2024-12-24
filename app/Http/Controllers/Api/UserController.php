<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($email)
    {
        $users = User::where('email', $email)->first();
        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    // update google id
    public function updateGoogleId(Request $request, $id)
    {
        $request->validate([
            'google_id' => 'required',
        ]);
        $user = User::find($id);

        if ($user) {
            $user->google_id = $request->google_id;
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Google ID updated',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created',
            'data' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'google_id' => 'required',
            'ktp_number' => 'required',
            'birth_date' => 'required',
            'gender' => 'required',
        ]);

        $user = User::find($id);
        $user->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'User updated',
            'data' => $user,
        ]);
    }

    // check email
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'Email already registered',
                'valid' => false,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not registered',
                'valid' => true,
            ]);
        }
    }

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    // logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
