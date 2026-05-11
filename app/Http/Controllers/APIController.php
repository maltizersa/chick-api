<?php

namespace App\Http\Controllers;

use Exception;
use Hash;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class APIController extends Controller
{
    public function hello(){
        return response()->json(['message' => 'Hello World!']);
    }

    public function bookHotel(Request $request){
        $data = $request->all();

        try{
            DB::insert(
                "INSERT INTO bookings (id, uid, hotel_id, room_type, check_in, check_out) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $data['id'],
                    $data['uid'],
                    $data['hotel_id'],
                    $data['room_type'],
                    $data['check_in'],
                    $data['check_out']
                ]
            );
            return response()->json(['success' => true, 'message' => 'Booking created successfully.']);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
                'uid' => $user->uid,
                'user' => $user,
            ]);
        }

        // Invalid credentials
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.'
        ]);
    }

    public function getBookingDetails($bookingID){

        $booking = DB::select(
            "SELECT 
                b.*, 
                h.hotel_name AS hotel_name,
                (SELECT price FROM hotel_rooms WHERE room_name = b.room_type) AS price
                FROM bookings b
                JOIN hotelsdb h ON b.hotel_id = h.id
                WHERE b.id = ?
            ",
            [
                $bookingID,
            ]
        );

        if($booking){
            return response()->json(['success' => true, 'booking' => $booking[0]]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Booking not found.']);
        }
    }
    public function verifyEmail(Request $request){
        $request -> validate([
            'first_name' => 'required',
            'last_name' => 'required',
            // 'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $users = DB::select("SELECT * FROM usersdb WHERE email = ?",
            [
                $request->email
            ]
        );

        if(count($users) > 0){
            return response()->json(['message' => 'Email already exists.', 'exists' => true]);
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
            'first_name' => 'required',
            'last_name' => 'required',
            // 'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        $data = $request->all();

        try{
            DB::insert(
                "
                    INSERT INTO usersdb
                    (first_name, middle_name, last_name, email, password, phone_number)
                    VALUES
                    (?, ?, ?, ?, ?, ?)
                ",
                [
                    $data['first_name'],
                    $data['middle_name'],
                    $data['last_name'],
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
        $hotels = DB::select("
            SELECT 
                h.id,
                h.hotel_name,
                h.hotel_address,
                h.hotel_contact,
                h.hotel_image_loc,
                h.hotel_longitude,
                h.hotel_latitude,
                h.owner_id,
                h.approved,
                h.status,
                h.created_at,
                GROUP_CONCAT(a.name) as amenities,
                (SELECT MIN(price) FROM hotel_rooms WHERE hotel_id = h.id) AS min_price,
                (SELECT MAX(price) FROM hotel_rooms WHERE hotel_id = h.id) AS max_price,
                COALESCE(
                (SELECT AVG(rating) FROM reviews WHERE hotel_id = h.id),
                0
                ) AS ratings
            FROM hotelsdb h
            LEFT JOIN hotel_amenities ha ON ha.hotel_id = h.id
            LEFT JOIN amenities a ON a.id = ha.amenity_id
            WHERE h.approved = 1
            GROUP BY 
                h.id,
                h.hotel_name,
                h.hotel_address,
                h.hotel_contact,
                h.hotel_image_loc,
                h.hotel_longitude,
                h.hotel_latitude,
                h.owner_id,
                h.approved,
                h.status,
                h.created_at;
        ");
        // dd($hotels);
        return response()->json(['hotels' => $hotels]);
    }

    public function fetchdetails($id)
    {
        $hotel = DB::table('hotelsdb as h')
            ->leftJoin('usersdb as u', 'h.owner_id', '=', 'u.uid')
            ->select(
                'h.*',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as owner_name")
            )
            ->where('h.id', $id)
            ->first();

        $reviews = DB::table('reviews as r')
            ->leftJoin('usersdb as u', 'r.uid', '=', 'u.uid')
            ->select(
                'r.*',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as user_name"),
            )
            ->where('r.hotel_id', $id)
            ->get();

        // ⭐ ADD THIS: average rating
        $avgRating = DB::table('reviews')
            ->where('hotel_id', $id)
            ->avg('rating');

        // optional: count
        $ratingCount = DB::table('reviews')
            ->where('hotel_id', $id)
            ->count();

        $amenities = DB::table('hotel_amenities')
            ->where('hotel_id', $id)
            ->get();

        return response()->json([
            'hotel' => $hotel,
            'reviews' => $reviews,
            'amenities' => $amenities,
            'average_rating' => (float) $avgRating,
            'rating_count' => $ratingCount
        ]);
    }
    public function myHotels($ownerId)
    {
        $hotels = DB::table('hotelsdb')
            ->where('owner_id', $ownerId)
            ->get();

        return response()->json([
            'hotels' => $hotels
        ]);
    }

    public function myBookings($uid)
    {
        $hotels = DB::table('bookings')
            ->join('hotelsdb', 'bookings.hotel_id', '=', 'hotelsdb.id')
            ->where('bookings.uid', $uid)
            ->where('bookings.status', '!=', 'completed')
            ->where('bookings.status', '!=', 'rated')
            ->select(
                'bookings.id as booking_id',
                'bookings.uid',
                'bookings.hotel_id',
                'bookings.room_type',
                'bookings.check_in',
                'bookings.check_out',
                'bookings.status as booking_status',

                // HOTEL INFO
                'hotelsdb.hotel_name',
                'hotelsdb.hotel_address',
                'hotelsdb.hotel_contact',
                'hotelsdb.hotel_image_loc',
                'hotelsdb.status as hotel_type',
                'hotelsdb.approved',
                'hotelsdb.created_at as hotel_created_at'
            )
            ->get();

        return response()->json([
            'hotels' => $hotels
        ]);
    }
    public function toRate($uid)
    {
        $hotels = DB::table('bookings')
            ->join('hotelsdb', 'bookings.hotel_id', '=', 'hotelsdb.id')
            ->where('bookings.uid', $uid)
            ->where('bookings.status', '=', 'completed')
            ->orWhere('bookings.status', '=', 'rated')
            ->select(
                'bookings.id as booking_id',
                'bookings.uid',
                'bookings.hotel_id',
                'bookings.room_type',
                'bookings.check_in',
                'bookings.check_out',
                'bookings.status as booking_status',

                // HOTEL INFO
                'hotelsdb.hotel_name',
                'hotelsdb.hotel_address',
                'hotelsdb.hotel_contact',
                'hotelsdb.hotel_image_loc',
                'hotelsdb.status as hotel_type',
                'hotelsdb.approved',
                'hotelsdb.created_at as hotel_created_at'
            )
            ->get();

        return response()->json([
            'hotels' => $hotels
        ]);
    }

    public function rateHotel(Request $request){
        $data = $request->all();
        try{
            DB::insert(
                "INSERT INTO reviews (uid, hotel_id, rating, comment) VALUES (?, ?, ? ,?)",
                [
                    $data['uid'],
                    $data['hotelID'],
                    $data['rating'],
                    $data['comment']
                ]
            );

            // print($data['booking_id']);

            // DB::statement(
            //     "UPDATE bookings SET status = 'rated' WHERE booking_id = ?",
            //     [
            //         $data['booking_id']
            //     ]
            // );

            DB::update(
                "
                    UPDATE bookings SET status = 'rated' WHERE id = ?
                ",
                [
                    $data['booking_id']
                ]
            );

            return response()->json([
                'success' => true
            ]);
        }
        catch(Exception $e){
            return response()->json(['success' => false, 'message' => $e]);
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

    public function addHotel(Request $request)
    {
        try {

            // ======================
            // VALIDATION
            // ======================
            $request->validate([
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'type' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'hotel_image' => 'required|image',
                'pdf_file' => 'required|mimes:pdf',
                'ownerid' => 'required|integer',
                'room_types' => 'nullable'
            ]);

            // ======================
            // FILE UPLOADS
            // ======================
            $imagePath = $request->file('hotel_image')->store('hotels', 'public');
            $pdfPath   = $request->file('pdf_file')->store('pdfs', 'public');

            $ownerId = (int) $request->ownerid;

            $type = strtolower(trim($request->type));

            $status = in_array($type, ['hotel', 'inn']) ? $type : 'hotel';

            // ======================
            // INSERT HOTEL
            // ======================
            DB::insert("
                INSERT INTO hotelsdb (
                    hotel_name,
                    hotel_address,
                    hotel_contact,
                    hotel_image_loc,
                    hotel_longitude,
                    hotel_latitude,
                    owner_id,
                    approved,
                    status,
                    created_at,
                    hotel_pdf_loc
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $request->name,
                $request->address,
                $request->phone,
                $imagePath,
                $request->longitude,
                $request->latitude,
                $ownerId,
                0,
                $status,
                now(),
                $pdfPath
            ]);

            $hotelId = DB::getPdo()->lastInsertId();

            $amenities = explode(',', $request->amenities);

            foreach ($amenities as $amenityId) {
                DB::insert(
                    "INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)",
                    [
                        $hotelId,
                        (int) $amenityId
                    ]
                );
            }

            // ======================
            // ROOMS
            // ======================
            $rooms = json_decode($request->room_types, true) ?? [];

            foreach ($rooms as $index => $room) {

                $roomImagePath = null;

                if ($request->hasFile("room_image_$index")) {
                    $roomImagePath = $request->file("room_image_$index")
                        ->store('rooms', 'public');
                }

                DB::insert("
                    INSERT INTO hotel_rooms (
                        hotel_id,
                        room_name,
                        price,
                        image_path
                    ) VALUES (?, ?, ?, ?)
                ", [
                    $hotelId,
                    $room['name'] ?? '',
                    $room['price'] ?? 0,
                    $roomImagePath
                ]);
            }

            return response()->json([
                'message' => 'Hotel created successfully',
                'hotel_id' => $hotelId
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function updateHotel(Request $request)
    {
        try {

            // ======================
            // VALIDATION
            // ======================
            $request->validate([
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'type' => 'required',
                // 'latitude' => 'required',
                // 'longitude' => 'required',
                // 'hotel_image' => 'required|image',
                // 'pdf_file' => 'required|mimes:pdf',
                'ownerid' => 'required|integer',
                'room_types' => 'nullable'
            ]);

            // ======================
            // FILE UPLOADS
            // ======================

            $hotel = DB::table('hotelsdb')->where('id', $request->hotel_id)->first();

            $imagePath = $hotel->hotel_image_loc;
            $pdfPath   = $hotel->hotel_pdf_loc;

            if ($request->hasFile('hotel_image')) {
                $imagePath = $request->file('hotel_image')->store('hotels', 'public');
            }
            

            if ($request->hasFile('pdf_file')) {
                $pdfPath = $request->file('pdf_file')->store('pdfs', 'public');
            }
           
            $type = strtolower(trim($request->type));

            $status = in_array($type, ['hotel', 'inn']) ? $type : 'hotel';

            // ======================
            // UPDATE HOTEL (PALITAN ANG INSERT)
            // ======================
            DB::update("
                UPDATE hotelsdb
                SET hotel_name = ?,
                    hotel_address = ?,
                    hotel_contact = ?,
                    hotel_image_loc = ?,
                    status = ?,
                    hotel_pdf_loc = ?
                WHERE id = ?
            ", [
                $request->name,
                $request->address,
                $request->phone,
                $imagePath,
                $status,
                $pdfPath,
                $request->hotel_id // IMPORTANT: dapat may hotel_id ka sa request
            ]);

            $hotelId = $request->hotel_id;

            // ======================
            // AMENITIES (DELETE THEN INSERT)
            // ======================

            // DELETE OLD AMENITIES
            DB::table('hotel_amenities')
                ->where('hotel_id', $hotelId)
                ->delete();

            // INSERT NEW AMENITIES
            $amenities = explode(',', $request->amenities);

            foreach ($amenities as $amenityId) {
                DB::insert("
                    INSERT INTO hotel_amenities (hotel_id, amenity_id)
                    VALUES (?, ?)
                ", [
                    $hotelId,
                    (int) $amenityId
                ]);
            }
            return response()->json([
                'message' => 'Hotel created successfully',
                'hotel_id' => $hotelId
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Server Error',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function fetchroomtypes($id){

        $roomtypes = DB::select(
            "SELECT * FROM hotel_rooms WHERE hotel_id = ?",
            [$id]
        );


        return response()->json([
            'roomtypes' => $roomtypes
        ]);
        
    }

    public function addRoom(Request $request)
    {
        try {

            $request->validate([
                'hotel_id' => 'required',
                'room_name' => 'required',
                'price' => 'required',
                'room_image' => 'nullable|image',
            ]);

            $imagePath = null;

            // ======================
            // IMAGE UPLOAD (SAME STYLE AS addHotel)
            // ======================
            if ($request->hasFile('room_image')) {
                $imagePath = $request->file('room_image')
                    ->store('rooms', 'public');
            }

            // ======================
            // INSERT ROOM
            // ======================
            DB::insert("
                INSERT INTO hotel_rooms (
                    hotel_id,
                    room_name,
                    price,
                    image_path,
                    created_at
                ) VALUES (?, ?, ?, ?, ?)
            ", [
                $request->hotel_id,
                $request->room_name,
                $request->price,
                $imagePath,
                now(),
            ]);

            $roomId = DB::getPdo()->lastInsertId();

            return response()->json([
                "message" => "Room added successfully",
                "room" => [
                    "room_id" => $roomId,
                    "room_name" => $request->room_name,
                    "price" => $request->price,
                    "image_path" => $imagePath,
                ]
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                "message" => "Server Error",
                "error" => $e->getMessage(),
                "line" => $e->getLine()
            ], 500);
        }
    }

    public function fetchMessages($uid)
    {
        $messages = DB::select("
            SELECT 
                u.uid,
                u.first_name,
                u.last_name,
                u.profile_picture,
                m.message AS last_message,
                m.created_at,
                m.sender_id,
                m.receiver_id,
                m.is_read
            FROM messages m
            JOIN usersdb u 
                ON u.uid = 
                    CASE 
                        WHEN m.sender_id = ? THEN m.receiver_id
                        ELSE m.sender_id
                    END
            WHERE m.message_id IN (
                SELECT MAX(message_id)
                FROM messages
                WHERE sender_id = ? OR receiver_id = ?
                GROUP BY 
                    LEAST(sender_id, receiver_id),
                    GREATEST(sender_id, receiver_id)
            )
            ORDER BY m.created_at DESC
        ", [$uid, $uid, $uid]);

        return response()->json($messages);
    }

    public function fetchChat($uid, $otherId)
    {
        $messages = DB::table('messages')
            ->where(function ($q) use ($uid, $otherId) {
                $q->where('sender_id', $uid)
                ->where('receiver_id', $otherId);
            })
            ->orWhere(function ($q) use ($uid, $otherId) {
                $q->where('sender_id', $otherId)
                ->where('receiver_id', $uid);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // 🔥 GET OTHER USER INFO
        $user = DB::table('usersdb')
            ->select('uid', 'first_name', 'last_name', 'profile_picture')
            ->where('uid', $otherId)
            ->first();

        return response()->json([
            "user" => $user,
            "messages" => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        DB::table('messages')->insert([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => 0,
            // 'created_at' => now()
        ]);

        return response()->json(['status' => 'sent']);
    }

    public function markRead(Request $request)
    {
        DB::table('messages')
            ->where('sender_id', $request->sender_id)
            ->where('receiver_id', $request->receiver_id)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1
            ]);

        return response()->json(['status' => 'updated']);
    }

    public function deleteRoom(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer'
        ]);

        $room = DB::table('hotel_rooms')
            ->where('room_id', $request->room_id)
            ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        DB::table('hotel_rooms')
            ->where('room_id', $request->room_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    }

    public function changepass(Request $request){
        $data = $request->all();
        
        $user = DB::table('usersdb')
            ->where('uid', $data['uid'])
            ->first();

        if(!$user){
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ]);
        }

        if(!Hash::check($data['old_password'], $user->password)){
            return response()->json([
                "success" => false,
                "message" => "Old password incorrect"
            ]);
        }

        DB::table('usersdb')
            ->where('uid', $data['uid'])
            ->update([
                'password' => Hash::make($data['new_password'])
            ]);

        return response()->json([
            "success" => true,
            "message" => "Password updated"
        ]);
    }

    public function updateProfileImage(Request $request)
    {
        $user = DB::table('usersdb')
            ->where('uid', $request->uid)
            ->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ]);
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . $request->uid . '.' . $extension;

            $path = $file->storeAs('profiles', $filename, 'public');

            DB::table('usersdb')
                ->where('uid', $request->uid)
                ->update([
                    "profile_picture" => $path
                ]);

            return response()->json([
                "success" => true,
                "image_url" => $path
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "No image uploaded"
        ]);
    }

    public function updateaccount(Request $request)
    {
        $data = $request->all();

        $users = DB::table("usersdb")
        ->where('uid', $data['uid'])
        ->update(
            [
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number'=> $data['phone_number'],
                'gcash_number' => $data['gcash_number'] ?? null
            ]
        );

        if($users)
        {
            return response()->json(["sucess" => true, "message" => "Personal Information updated."]);
        }
        else
        {
            return response()->json(["false" => true, "message" => "Personal Information update failed."]);
        }
    }

    public function notifcount($uid){
        $notifcount = DB::table("notifications")
        ->where("uid", $uid)
        ->where("seen", 0)
        ->count();

        return response()->json(["count" => $notifcount]);
    }

    public function getnotif($uid){
        $notifcount = DB::table("notifications")
        ->where("uid", $uid)
        ->orderBy('created_at', 'desc')
        ->get();

        $this->markSeen($uid);

        return response()->json(["notifications" => $notifcount]);
    }

    public function markSeen($uid){
        DB::table("notifications")
        ->where("uid", $uid)
        ->where("seen", 0)
        ->update([
            "seen" => 1
        ]);
    }

    public function createNotification(Request $request)
    {
        $request->validate([
            'uid' => 'required|integer',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $notif = DB::table('notifications')->insert([
            'uid' => $request->uid,
            'title' => $request->title,
            'message' => $request->message,
            'seen' => 0,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully'
        ]);
    }
}
