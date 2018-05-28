<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   	public function showSettings(){
   		$rs = DB::select('SELECT * FROM users WHERE id = ?',[auth()->user()->id]);
   		$logs = DB::select('SELECT * FROM logs WHERE userID = ?',[auth()->user()->id]);
      // var_dump($logs);
   		return view('settings')->with('detail',$rs[0])->with('logs',$logs);
   	}

   	public function updateSettings(Request $request){
   		DB::table('users')->where('id',auth()->user()->id)->update(['name'=>$request->name, 'age'=>$request->age]);
   		return redirect('/settings')->with('success','Account Updated');
   	}

   	public function showChangingPasswordForm(){
   		return view('auth.changePassword');
   	}

    public function changePassword(Request $request){
 
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
 
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
 
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
 
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
 
        return redirect('/settings')->with("success","Password changed successfully !");
 
    }
}
