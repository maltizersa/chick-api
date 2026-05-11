<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class WEBController extends Controller
{
    public function showlogin(){
        return view("login");
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($request->username === 'admin123' && $request->password === 'admin123123') {
            session([
                "login" => true
            ]);
            return redirect('/addhotel');
        }

        return redirect('/admin/login')->withErrors([
            'login' => 'Invalid username or password'
        ]);
    }

    public function home()
    {
        if (!session()->has("login")) {
            return redirect('/admin/login');
        }

        $hotels = DB::table("hotelsdb")
            ->join("usersdb", "hotelsdb.owner_id", "=", "usersdb.uid")
            ->select(
                "hotelsdb.*",
                "usersdb.first_name",
                "usersdb.middle_name",
                "usersdb.last_name"
            )
            ->where("hotelsdb.approved", 0)
            ->orderBy("hotelsdb.created_at", "desc")
            ->get();

        return view("addhotel", compact("hotels"));
    }

    public function approve($id)
    {
        $hotel = DB::table('hotelsdb')->where('id', $id)->first();

        DB::table('hotelsdb')
            ->where('id', $id)
            ->update(['approved' => 1]);

        // INSERT NOTIFICATION
        $this->insertNotification(
            $hotel->owner_id,
            "Hotel Approved",
            "Your hotel '{$hotel->hotel_name}' has been approved."
        );

        return back();
    }

    public function deny($id)
    {
        $hotel = DB::table('hotelsdb')->where('id', $id)->first();

        DB::table('hotelsdb')
            ->where('id', $id)
            ->delete();

        // INSERT NOTIFICATION
        $this->insertNotification(
            $hotel->owner_id,
            "Hotel Rejected",
            "Your hotel '{$hotel->hotel_name}' has been rejected."
        );

        return back();
}

    public function insertNotification($uid, $title, $message)
    {
        DB::table('notifications')->insert([
            'uid' => $uid,
            'title' => $title,
            'message' => $message,
            'seen' => 0,
            'created_at' => now()
        ]);
    }

    public function hotelowners()
    {
        if (!session()->has("login")) {
            return redirect('/admin/login');
        }

        $owners = DB::table('usersdb')
            ->select(
                'uid',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'phone_number',
                'gcash_number',
                'profile_picture'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return view("hotelowners", compact("owners"));
    }

    public function logout(){
        session()->flush();

        return redirect('/admin/login');
    }
}
