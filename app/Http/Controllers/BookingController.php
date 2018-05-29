<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Booking;
use App\UsersQueue;
use DB;
use Carbon\Carbon;

class BookingController extends MailController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function book(Request $request)
    {
    	$booking = new Booking();
    	$booking->eventID = $request->eventID;
    	$booking->userID = auth()->user()->id;
    	$booking->save();
        DB::table('events')->where('id',$request->eventID)->decrement('slotsLeft');
        //Remove this user out of event queue if he has joined in
        $affectedRows = DB::delete("DELETE FROM queue WHERE eventID = ? AND userID = ?",[$request->eventID , auth()->user()->id]);
        $this->bookingNotify($request,$request->eventID);
        $this->reminderNotify($request);
        $event = DB::select("SELECT * FROM events WHERE id = ?",[$request->eventID]);
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'booked event '.$event[0]->title, 'timestamp'=>$date->toDateTimeString()]);
    	return redirect('/events/'.$request->eventID.'/student')->with('success','Successfully book in event');
    }

    public function cancel(Request $request)
    {
    	DB::delete("DELETE FROM booking WHERE eventID = ? AND userID = ?", [$request->eventID, auth()->user()->id]);
        DB::delete("DELETE FROM jobs WHERE payload LIKE '%i:3%' AND payload LIKE '%?%'" ,[auth()->user()->email]);
        DB::table('events')->where('id',$request->eventID)->increment('slotsLeft');
        //Select the number of slots left 
        $slotsLeft = DB::table('events')->where('id',$request->eventID)->value('slotsLeft');
        //If there are still enough slots, send invitation email to users in the waiting queue
        $emails=array();
        if($slotsLeft > 0){
            $emails = DB::table('queue')->select('email')->where('eventID',$request->eventID)->get();
            if($emails != null)
                $this->inviteUsers($request,$emails);
        }
        $this->cancelingNotify($request,$request->eventID);
        $event = DB::select("SELECT * FROM events WHERE id = ?",[$request->eventID]);
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'canceled event '.$event[0]->title, 'timestamp'=>$date->toDateTimeString()]);
    	return redirect('/events/'.$request->eventID.'/student')->with('success','Successfully cancel booking event');
    }

    public function enqueue(Request $request)
    {
        $queue = new UsersQueue();
        $queue->eventID = $request->eventID;
        $queue->userID = auth()->user()->id;
        $queue->email = auth()->user()->email;
        $queue->save();
        $this->joinQueueNotify($request,$request->eventID);
        $event = DB::select("SELECT * FROM events WHERE id = ?",[$request->eventID]);
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'joined the waiting list for event '.$event[0]->title, 'timestamp'=>$date->toDateTimeString()]);
        return redirect()->back()->with('success','Successfully joining the queue');
    }

    public function dequeue(Request $request){
        $affectedRows = DB::delete("DELETE FROM queue WHERE eventID = ? AND userID = ?",[$request->eventID , auth()->user()->id]);
        $date = new Carbon();
        $event = DB::select("SELECT * FROM events WHERE id = ?",[$request->eventID]);
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'canceled waiting list for event '.$event[0]->title, 'timestamp'=>$date->toDateTimeString()]);
        return redirect()->back()->with('success','Successfully leaving the queue');
    }
}
