<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Absent;
use App\Jobs\ProcessUnban;
use DB;
use Carbon\Carbon;


class AbsenceController extends MailController
{
    public function store(Request $request){
    	$users = $request->input('absentee');
    	$eventID = $request->input('event');
    	DB::table('events')->where('id',$eventID)->update(['isFinalized'=>1]);
    	//If there are some absentees
    	if($users != null){
	    	foreach ($users as $absentee){
	   	    	$username = DB::table('users')->select('name')->where('id','=',$absentee)->first();
	    		DB::table('absent')->insert(['eventID'=>$eventID,'userID'=>$absentee]);
		    	$absentRs = DB::select(
		    		'SELECT count(*) as absentTimes FROM events INNER JOIN absent
		    		ON events.id = absent.eventID
		    		INNER JOIN users
		    		ON absent.userID = users.id
		    		WHERE userID = ? AND (events.time >= unbannedDate OR unbannedDate IS NULL)
		    		GROUP BY userID',[$absentee, date("Y-m-d H:i:s")]
		    	);
		    	//Get email of this user
		    	$emailRs = DB::table('users')->select('email')->where('id','=',$absentee)->first();
		    	//Send absent reminder
		    	$this->absentReminder($eventID,$emailRs->email, $username->name);
		    	//If user has been absent more than 3 times , he get banned
		    	if($absentRs[0]->absentTimes > 3){
		    		DB::table('users')->where('id',$absentee)
		    						 ->update(['isBanned'=>1]);
		    		$this->banNotification($eventID,$emailRs->email,$username->name);
		    		$when = Carbon::now()->addWeeks(4);
					//Set unbannedDate
			        DB::table('users')->where('id','=',$absentee)->update(['unbannedDate'=>$when]);
		    		//ProcessUnban job, unban user after 4 weeks
		    		ProcessUnban::dispatch($absentee)->delay($when);

		    	}
	    	}
	    	//Get contact detail of attendees
	    	$contacts = DB::table('booking')->join('users','booking.userID','=','users.id')->join('events','booking.eventID','=','events.id')
	    	->select('users.email','users.name','events.title as event')->whereNotIn('userID',$users)->where('eventID','=',$eventID)->get();
    	}
    	//If everyone attends to events, get contacts of everyone
    	else{
	    	$contacts = DB::table('booking')->join('users','booking.userID','=','users.id')->join('events','booking.eventID','=','events.id')
	    	->select('users.email','users.name','events.title as event')->where('eventID','=',$eventID)->get();
    	}
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'checked attendance', 'timestamp'=>$date->toDateTimeString()]);
    	//If there are some attendees, view form to send follow-up mail 
    	if($contacts->count()){
    		return view('form.SendFollowingMail')->with('contacts',$contacts);
    	}
    	//No one goes , redirect to events home
    	else{
    		return redirect('/events')->with('success','No one participates in events');
    	}

    }
}
