<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'succes' => false,
                'message' => $validation->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role' => 'user',
            'verification_status' => 'unverified',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->authPayload($user),
            'message' => 'User register succesfully',
        ], 200);
    }

    public function login(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 422);
        }

        if (! Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            return response()->json([
                'success' => false,
                'message' => 'email or password in correct',
            ]);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $this->authPayload($user),
        ], 200);
    }

    public function Vender_register(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'nullable|in:vender',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'succes' => false,
                'message' => $validation->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role' => 'vender',
            'verification_status' => 'unverified',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->authPayload($user),
            'message' => 'User register succesfully',
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    public function submitVerification(Request $request)
    {
        $user = $request->user();
        $rules = $user->role === 'vender'
            ? [
                'business_name' => 'required|string|max:255',
                'business_type' => 'required|string|max:255',
                'business_address' => 'required|string|max:1000',
                'tax_id' => 'required|string|max:255',
                'document_url' => 'required|url|max:1000',
            ]
            : [
                'phone' => 'required|string|max:30',
                'address' => 'required|string|max:1000',
                'national_id' => 'required|string|max:255',
                'document_url' => 'required|url|max:1000',
            ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors(),
            ], 422);
        }

        $user->update([
            'verification_data' => $validation->validated(),
            'verification_status' => 'pending',
            'verification_note' => null,
            'verification_submitted_at' => now(),
             'verification_reviewed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification submitted successfully.',
            'data' => $user->fresh(),
        ]);
    }

    public function listByRole(string $role)
    {
        $normalizedRole = $role === 'venders' ? 'vender' : $role;
        $users = User::query()
            ->when($normalizedRole === 'vender', function ($query) {
                $query->whereIn('role', ['vender', 'vender']);
            }, function ($query) use ($normalizedRole) {
                $query->where('role', $normalizedRole);
            })
            ->select('id', 'name', 'email', 'role', 'verification_status', 'verification_note', 'verification_data', 'verification_submitted_at', 'verification_reviewed_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function updateVerificationStatus(Request $request, User $user)
    {
        $validation = Validator::make($request->all(), [
            'verification_status' => 'required|in:verified,rejected',
            'verification_note' => 'nullable|string|max:1000',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors(),
            ], 422);
        }

        $user->update([
            'verification_status' => $request->verification_status,
            'verification_note' => $request->verification_status === 'rejected' ? $request->verification_note : null,
            'verification_reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification status updated.',
            'data' => $user->fresh(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    private function authPayload(User $user): array
    {
        return [
            'token' => $user->createToken('MyApp')->plainTextToken,
            'name' => $user->name,
            'role' => $user->role,
            'verification_status' => $user->verification_status,
        ];
    }
}
