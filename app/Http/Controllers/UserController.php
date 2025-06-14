<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get all users (only accessible by admin)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
} 