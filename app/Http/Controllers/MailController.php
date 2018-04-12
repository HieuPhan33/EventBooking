<?php

namespace App\Http\Controllers;

use App\Event;
use App\Mail\sendBookingNoti;
use App\Mail\sendCancelingNoti;
use App\Mail\sendInvitation;
use App\Mail\sendEventInformation;
use App\Mail\sendJoinQueueNoti;
use App\Mail\sendReminderNoti;
use App\Mail\sendAbsentReminder;
use App\Mail\sendBanNoti;
use App\Mail\sendFollowUp;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class MailController extends Controller
{
    //Notify users of booking receipt
    public function bookingNotify(Request $request,$eventID){
        $event = DB::select(
            "SELECT id , title , location , time ,'".auth()->user()->name."' AS username 
            FROM events
            WHERE events.id = ?",
            [$eventID , auth()->user()->id]
        );
    	Mail::to($request->user())
    	->queue(new sendBookingNoti($event[0]));
    	return $event;
    }

    //Notify users of canceling receipt
    public function cancelingNotify(Request $request,$eventID){
        $event = DB::select(
            "SELECT id , title , location , time ,'".auth()->user()->name."' AS username 
            FROM events
            WHERE events.id = ?",
            [$eventID , auth()->user()->id]
        );
    	Mail::to($request->user())
    	->queue(new sendCancelingNoti($event[0]));
    	return $event;
    }

    //Notify users of joining queue status
    public function joinQueueNotify(Request $request,$eventID){
        $event = DB::select(
            "SELECT id , title , location , time ,'".auth()->user()->name."' AS username 
            FROM events
            WHERE events.id = ?",
            [$eventID , auth()->user()->id]
        );
        Mail::to($request->user())
        ->queue(new sendJoinQueueNoti($event[0]));
    }

    public function reminderNotify(Request $request){
        $event = DB::select(
            "SELECT id , title , location , time ,'".auth()->user()->name."' AS username 
            FROM events
            WHERE events.id = ?",
            [$request->eventID , auth()->user()->id]
        );
        $date = $event[0]->time;
        //Send one day before event starts
        $when = Carbon::parse($date)->subDays(1);

        Mail::to($request->user())
        ->later($when , new sendReminderNoti($event[0]));
        return "will be sent at ".$when;
    }

    //Invite users in waiting list to book in after someone cancel booking
    public function inviteUsers(Request $request, $emails){
        foreach($emails as $email){
            $event = DB::select(
                'SELECT events.id , title , location , time , name AS username
                FROM events INNER JOIN queue
                ON events.id = queue.eventID
                INNER JOIN users
                ON queue.userID = users.id
                WHERE events.id = ? AND users.email = ?
                ',[$request->eventID , $email->email]
            );
            Mail::to($email->email)
            ->queue(new sendInvitation($event[0]));
        }
    }

    // Inform users who interested in new events
    public function informUsers($eventID,$emails){
        $newEvent = DB::select('SELECT id,title,location, DATE_FORMAT(time, "%W %e %M %Y") as time from events where id = ?',[$eventID]);
        foreach($emails as $email){
            $username = DB::select('select name from users where email = ?',[$email->email]);
            Mail::to($email->email)
            ->queue(new sendEventInformation($newEvent[0],$username[0]));
        }
    }

    public function absentReminder($eventID , $email ,$username){
        $event = DB::select(
            "SELECT id , title , location , time ,'".$username."' AS username 
            FROM events
            WHERE events.id = ?",
            [$eventID]
        );
        Mail::to($email)
        ->queue(new sendAbsentReminder($event[0]));
    }

    public function banNotification($eventID, $email, $username){
        $event = DB::select(
            "SELECT id , title , location , time ,'".$username."' AS username 
            FROM events
            WHERE events.id = ?",
            [$eventID]
        );
        Mail::to($email)
        ->queue(new sendBanNoti($event[0]));
    }

    public function sendFollowingUpMail(Request $request){
      $file = $request->file('uploaded_file');
      $input = array();
      $input['event']=$request->input['event'];
      $input['title'] = $request->input('title');
      $input['message'] = $request->input('message');
      $input['type'] = $file->getMimeType();
      $input['filename'] = $file->getClientOriginalName();
      //Move Uploaded File
      $destinationPath = public_path().'\uploads';
      $path = $file->move($destinationPath,$file->getClientOriginalName());
      $input['path'] = $path;
      $emails = explode(",",$request->input('emails')[0]);
      $names = explode(",",$request->input('names')[0]);
      for($i = 0; $i < count($emails) ;$i++){
        $input['name'] = $names[$i];
        Mail::to($emails[$i])->send(new sendFollowUp($input));
      }
      return redirect('/events');
    }
}
