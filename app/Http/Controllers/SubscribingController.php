<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class SubscribingController extends Controller
{
    public function show(){
    	$categories = DB::select('SELECT categories.name, A.userID, categories.id FROM categories LEFT JOIN
                        (SELECT categoryID,userID FROM subscribe
                        WHERE userID = ?) A
                        ON categories.id = A.categoryID
                        ORDER BY categories.name',[auth()->user()->id]);
    	return view('subscription')->with('categories',$categories);
    }

    public function store(Request $request){
    	DB::table('subscribe')->where('userID','=',auth()->user()->id)->delete();
    	$subscriptions = $request->input('subscribe');
        if($subscriptions != null){
        	foreach($subscriptions as $subscription){
        		DB::table('subscribe')->insert([
        			"categoryID"=>$subscription,
        			"userID"=>auth()->user()->id]);
        	} 
            $date = new Carbon();
            DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'subscribed categories', 'timestamp'=>$date->toDateTimeString()]);
    	   return redirect('/events')->with('success','Succesfully subscribe, you will get email for upcoming interesting events');
        }
        else
            return redirect('/events')->with('success','Succesfully unsubscribe');
    }
}
