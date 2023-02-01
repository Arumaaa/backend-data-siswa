<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    /**
     * Forgot password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $email = $request->input('email');

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 400);
        }

        // Generate password reset token
        $token = Hash::make($email . time());
        $user->reset_token = $token;
        $user->save();

        // Send password reset email
        $this->sendPasswordResetEmail($email, $token);

        return response()->json(['message' => 'Password reset email sent'], 200);
    }

    /**
     * Send password reset email to user.
     *
     * @param  string  $email
     * @param  string  $token
     * @return void
     */
    private function sendPasswordResetEmail($email, $token)
    {
        $client = new Client();
        $response = $client->post(
            'https://mailtrap.io/api/v1/inboxes/2081499/messages',
            [
                'auth' => [
                    '{1f2121df06a673}',
                    '{f86e27bcaaad17}'
                ],
                'form_params' => [
                    'to_address' => $email,
                    'subject' => 'Password Reset Request',
                    'html' => 'Please follow the link below to reset your password:<br><br>' .
                        '<a href="http://localhost:8000/reset-password?token=' . $token . '">http://localhost:8000/reset-password?token=' . $token . '</a>'
                ]
            ]
        );
    }
}
