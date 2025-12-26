<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = $request->user();
        
        // For now, allow immediate verification
        // In production, you would verify the uploaded documents first
        $user->update(['verified' => true]);
        
        return response()->json([
            'message' => 'Account verified successfully',
            'user' => $user->fresh(),
        ]);
    }

    public function checkStatus(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'verified' => $user->verified ?? false,
            'user' => $user,
        ]);
    }

    // Admin endpoint to verify any user (for testing/admin purposes)
    public function adminVerify(Request $request, $userId)
    {
        // In production, add admin role check here
        // if (!$request->user()->isAdmin()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }
        
        $user = User::findOrFail($userId);
        $user->update(['verified' => true]);
        
        return response()->json([
            'message' => 'User verified successfully',
            'user' => $user,
        ]);
    }
}

