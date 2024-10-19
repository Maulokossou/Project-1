<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Association;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AssociationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'field_of_activity' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $checkEmail = Association::where('email', '=', $request->email)->get()->first();

        if ($checkEmail) {
            return response()->json(['message' => 'Email already exists'], 409);
        }

        $association = Association::create([
            'name' => $request->name,
            'field_of_activity' => $request->field_of_activity,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false,
            'otp' => $this->generateOtp(),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($association->email)->send(new OtpMail($association->otp));

        return response()->json(['message' => 'Association registered. Please check your email for OTP.'], 201);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $association = Association::where('email', $request->email)->first();

        if (!$association) {
            return response()->json(['message' => 'Association not found'], 404);
        }

        if ($association->otp_attempts >= 3 && $association->last_otp_attempt->addMinutes(5)->isFuture()) {
            return response()->json(['message' => 'Too many attempts. Please try again in 5 minutes.'], 429);
        }

        if ($association->otp == $request->otp && $association->otp_expires_at->isFuture()) {
            $association->is_active = true;
            $association->otp = null;
            $association->otp_expires_at = null;
            $association->otp_attempts = 0;
            $association->save();

            return response()->json(['message' => 'Account activated successfully'], 200);
        } else {
            $association->otp_attempts += 1;
            $association->last_otp_attempt = Carbon::now();
            $association->save();

            return response()->json(['message' => 'Invalid OTP'], 400);
        }
    }

    private function generateOtp()
    {
        return sprintf("%06d", mt_rand(1, 999999));
    }
}