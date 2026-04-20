<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->id]);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                ]);
            }

            Auth::login($user);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login failed.');
        }
    }

    // Clerk Login
    public function redirectToClerk()
    {
        return Socialite::driver('clerk')->redirect();
    }

    public function handleClerkCallback()
    {
        try {
            $clerkUser = Socialite::driver('clerk')->user();

            $user = User::where('clerk_id', $clerkUser->id)
                ->orWhere('email', $clerkUser->email)
                ->first();

            if ($user) {
                $user->update(['clerk_id' => $clerkUser->id]);
            } else {
                $user = User::create([
                    'name' => $clerkUser->name,
                    'email' => $clerkUser->email,
                    'clerk_id' => $clerkUser->id,
                ]);
            }

            Auth::login($user);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \Log::error('Clerk login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Clerk login failed.');
        }
    }

    // Mobile OTP Login
    public function sendOtp(Request $request)
    {
        $request->validate(['mobile_number' => 'required|string']);

        $otp = '785699'; // Static OTP for testing
        $user = User::firstOrCreate(['mobile_number' => $request->mobile_number], [
            'name' => 'User ' . substr($request->mobile_number, -4),
            'email' => $request->mobile_number . '@mobile.user', // Placeholder email
        ]);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Simulated SMS sending
        session(['otp_mobile' => $request->mobile_number]);
        \Log::info("OTP for {$request->mobile_number}: {$otp}");

        return response()->json(['success' => true, 'message' => 'OTP sent successfully! (Check logs for simulated OTP)']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'otp' => 'required|string',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if ($user) {
            $user->update(['otp' => null, 'otp_expires_at' => null]);
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
