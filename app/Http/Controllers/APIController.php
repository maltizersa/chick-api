<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class APIController extends Controller
{
    public function hello(){
        return response()->json(['message' => 'Hello World!']);
    }

    public function verifyEmail(Request $request){
        $request -> validate([
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $code = rand(100000, 999999);

        $this->sendMail($request->email, $code); 

        return response()->json(['code' => $code]); 
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
                    $data['password'],
                    $data['phone_number'],
                ]
            );
            return response()->json(['success' => true]);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
