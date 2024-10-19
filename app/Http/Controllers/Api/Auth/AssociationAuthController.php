<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Association;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AssociationAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $association = Auth::user();
            if (!$association->is_active) {
                Auth::logout();
                return response()->json(['message' => 'Account is not activated. Please verify your email.'], 401);
            }
            $token = $association->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $association = Association::where('email', $request->email)->first();

        if (!$association) {
            return response()->json(['message' => 'Association not found'], 404);
        }

        $otp = $this->generateOtp();
        $association->otp = $otp;
        $association->otp_expires_at = Carbon::now()->addMinutes(10);
        $association->save();

        Mail::to($association->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'Password reset OTP sent to your email'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $association = Association::where('email', $request->email)->first();

        if (!$association) {
            return response()->json(['message' => 'Association not found'], 404);
        }

        if ($association->otp != $request->otp || $association->otp_expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $association->password = Hash::make($request->password);
        $association->otp = null;
        $association->otp_expires_at = null;
        $association->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    private function generateOtp()
    {
        return sprintf("%06d", mt_rand(1, 999999));
    }
}