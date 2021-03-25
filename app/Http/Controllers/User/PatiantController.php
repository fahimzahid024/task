<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User\patiant\Patiant;
use App\User\patiant\ticketBook;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\verifyEmail;
use Carbon\Carbon;
use Session;
use DB;

class PatiantController extends Controller
{
    public function signup(){
        return view('user.signup');
    }

    public function signin(){
        return view('user.signin');
    }
    
    public function save_patiant(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:patiants,email',
            'password' => 'required|min:6|confirmed'
        ]);
        $patiant = new Patiant();
        $patiant->name = $request->name;
        $patiant->email = $request->email;
        $patiant->password = md5($request->password);
        $patiant->email_verification_token = Str::random(32);
        try{
            $patiant->save();
            Mail::to($patiant->email)->send(new verifyEmail($patiant));
            Session::flash('message','Patiant saved Successfully!');
            Session::flash('type','success');
            return redirect()->back();

        }catch(\Exception $e){
            Session::flash('message','Patiant Not saved Successfully!');
            Session::flash('type','danger');
            return redirect()->back();

        }

    }

    public function verified_now($token){
        if($token === null)
        {
            Session::flash('message', 'Sign Up again!');
            Session::flash('type', 'warning');
            return redirect()->route('signup');
        }
        $patiant = Patiant::where('email_verification_token',$token)->first();

        if($patiant == null)
        {
            Session::flash('message', 'Sign Up again!');
            Session::flash('type', 'warning');
            return redirect()->route('signup');
        }

        try
        {
            $update = DB::table('patiants')
                  ->where('email_verification_token',$token)
                  ->update(['email_verified' => '1',
                  'email_verified_at' => Carbon::now(),
                  'email_verification_token' => '',
                  ]);
        
        // $user->update([
        //     '
        //     'email_verified_at' => Carbon::now(),
        //     'email_verification_token' => '',
        // ]);



        Session::flash('message', 'Your Account Is Activated!');
            Session::flash('type', 'sucess');
            return redirect()->route('signin');
        }
        catch(\Exception $e)
        {
            Session::flash('message', 'Sign up again!');
            Session::flash('type', 'danger');
            return redirect()->route('signup');
        }
    }

    public function login_patiant(Request $request){
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        $email = $request->email;
        $password = MD5($request->password) ;
    
        $result = DB::table('patiants')
                ->where('email',$email)
                ->where('password',$password)
                ->first();

        $user_name =  $result->name;
        $user_id = $result->id;
        $user_verified = $result->email_verified;

        if($user_verified === 0){
            Session::flash('message','Email is not verified!');
            Session::flash('type','danger');
            return redirect()->back();
        }
        if($user_name == null){
            Session::flash('message','Email or Password Not Match!');
            Session::flash('type','danger');
            return redirect()->back(); 
            
        }
        else{
            Session::put('patient_name',$user_name);
            Session::put('patient_id',$user_id);
            Session::flash('message','Logged in successfully!');
            Session::flash('type','success');
            return redirect()->back();
        }
        
    }

    public function logout(){
        Session::flush();
        return redirect()->route('/');
    }

    public function patient_booking($id){
        return view('user.doctor.form')->with('id',$id);
    }

    public function save_ticket_booking(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
        ]);
        // dd($_REQUEST);
        $patient_booking = new ticketBook();
        $patient_booking->name = $request->name;
        $patient_booking->email = $request->email;
        $patient_booking->doctor_id = $request->id;
        try{
            $patient_booking->save();
            Session::flash('message','Patient Ticket Booking successfully!');
            Session::flash('type','success');
            return redirect()->route('/');

        }catch(\Exception $e){
            Session::flash('message',$e->getMessage());
            Session::flash('type','danger');
            return redirect()->back();
        }
    }

    // public function test()
    // {

    //     $encrypted = \Crypt::encryptString('Hello world.');

    //     $decrypted = \Crypt::decryptString($encrypted);
    //     dd( $encrypted,$decrypted );
    // }
}
