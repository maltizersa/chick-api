<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class APIController extends Controller
{
    public function hello(){
            return response()->json(['message' => 'Hello World!']);
        }

        // ========== [ LOGIN FUNCTION ] ================
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get user by email
        $user = DB::table('usersdb')->where('email', $request->email)->first();

        // Check if user exists and password matches
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => true,
                'uid' => $user->uid
            ]);
        }

        // Invalid credentials
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.'
        ]);
    }

    public function verifyEmail(Request $request){
        $request -> validate([
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $users = DB::select("SELECT * FROM usersdb WHERE username = ?",
            [
                $request->username
            ]
        );

        if(count($users) > 0){
            return response()->json(['message' => 'Username already exists.', 'exists' => true]);
        }

        $code = rand(100000, 999999);

        $this->sendMail($request->email, $code); 

        return response()->json(['code' => $code]); 
    }

    public function forgotPassword(Request $request){

        $data = $request->all();

        $users = DB::select(
            "SELECT * FROM usersdb WHERE email = ?",
            [
                $data['email']
            ]
        );

        if(count($users) < 1){
            return response()->json(['success' => false, 'message' => 'Email not found.']);
        }
        else{

            $code = rand(100000, 999999);

            $this->sendMail($request->email, $code); 

            return response()->json(['code' => $code, 'success' => true, 'message' => 'Verification code sent to email.']); 
        }

    }

    public function resetPassword(Request $request){
        $data = $request->all();

        try{
            DB::statement(
                "UPDATE usersdb SET password = ? WHERE email = ?",
                [
                    Hash::make($data['password']),
                    $data['email']
                ]
            );
            return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function register(Request $request){
        $request -> validate([
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $data = $request->all();

        try{
            DB::insert(
                "
                    INSERT INTO usersdb
                    (username, email, password, phone_number)
                    VALUES
                    (?, ?, ?, ?)
                ",
                [
                    $data['username'],
                    $data['email'],
                    Hash::make($data['password']),
                    $data['phone_number'],
                ]
            );
            return response()->json(['success' => true ,'password' => Hash::make($data['password'])]);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function fetchHotels(){
        $hotels = DB::select("SELECT * FROM hotelsdb");

        return response()->json(['hotels' => $hotels]);
    }

    // ========== [ Compose Email ] ================
    public function sendMail($email, $code)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sibayanhandicraft@gmail.com';
            $mail->Password   = 'deqj bvhz hbeu nuaz';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('sibayanhandicraft@gmail.com', 'Chick IN');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Application Verification Code';
            $mail->Body    = "Your verification code is: $code";

            $mail->send();
            // echo 'Message has been sent';
        } catch (\Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
