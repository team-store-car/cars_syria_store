<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|string|in:admin,user,workshop,shop_manager'
        ]);
    
      
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        
            $user->assignRole($user->role);
    
        return response()->json([
            'message' => 'تم تسجيل الحساب بنجاح',
            'user'    => $user,
            'token'   => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }
    
    
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'بيانات تسجيل الدخول غير صحيحة'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user'    => $user,
            'token'   => $token
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    /**
     * استرجاع بيانات المستخدم الحالي
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }
}
