<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\sendBookingNoti;
class BuyTicketController extends MailController
{
	public function checkout($eventID, Request $request){
    	$quantity = $request->input('quantity');
        $isPromoted = $request->input('isPromoted');
        echo $isPromoted;
    	$event=DB::select('SELECT * from events WHERE events.id = ?',[$eventID]);
    	$time=Carbon::parse($event[0]->time)->toDayDateTimeString();
        $data = array('event'=>$event[0], 'quantity'=>$quantity, 'time'=>$time , 'isPromoted'=>$isPromoted);
    	return view('form.Payment')->with($data);
	}
    public function pay($eventID,Request $request){
    	$quantity = $request->input('quantity');
    	$total = $request->input('price') * $quantity;
    	DB::table('buy')->insert(
    		['eventID'=>$eventID, 'userID' => auth()->user()->id , 'quantity' => $quantity , 'total' => $total]
    	);
    	DB::table('booking')->insert(
    		['eventID'=>$eventID, 'userID' => auth()->user()->id]
    	);
        //Remove this user out of event queue if he has joined in
        $affectedRows = DB::delete("DELETE FROM queue WHERE eventID = ? AND userID = ?",[$request->eventID , auth()->user()->id]);
        $this->bookingNotify($request,$eventID);
    	return redirect('/events/'.$eventID.'/student')->with('success','Successfully buy ticket');
    }
}
