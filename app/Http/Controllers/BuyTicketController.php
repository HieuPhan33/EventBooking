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
        $code = $request->input('promoCode');
        $type = $request->input('type');
    	$event=DB::select('SELECT * from events WHERE events.id = ?',[$eventID]);
    	$time=Carbon::parse($event[0]->time)->toDayDateTimeString();
        $data = array('event'=>$event[0], 'quantity'=>$quantity, 'time'=>$time , 'code'=>$code, 'type'=>$type);
    	return view('form.Payment')->with($data);
	}
    public function pay($eventID,Request $request){
    	$code = $request->input('code');
    	$total = $request->input('total');
        $quantity = $request->input('quantity');
    	DB::table('buy')->insert(
    		['eventID'=>$eventID, 'userID' => auth()->user()->id , 'quantity' => $quantity , 'total' => $total]
    	);
        DB::table('events')->where('id',$eventID)->decrement('slotsLeft');
    	// DB::table('booking')->insert(
    	// 	['eventID'=>$eventID, 'userID' => auth()->user()->id]
    	// );
        //Delete the Promotional Code if user enters
        if($code != null){
            DB::table('promo_codes')->where('id','=',$code)->delete();
        }
        //Remove this user out of event queue if he has joined in
        $affectedRows = DB::delete("DELETE FROM queue WHERE eventID = ? AND userID = ?",[$request->eventID , auth()->user()->id]);
        $this->bookingNotify($request,$eventID);
    	return redirect('/events/'.$eventID.'/student')->with('success','Successfully buy ticket');
    }
}
