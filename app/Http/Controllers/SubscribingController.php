<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SubscribingController extends Controller
{
    public function show(){
    	$categories = DB::select('SELECT * from categories');
    	return view('subscription')->with('categories',$categories);
    }

    public function store(Request $request){
    	DB::table('subscribe')->where('userID','=',auth()->user()->id)->delete();
    	$subscriptions = $request->input('subscribe');
    	foreach($subscriptions as $subscription){
    		DB::table('subscribe')->insert([
    			"categoryID"=>$subscription,
    			"userID"=>auth()->user()->id]);
    	}
    	return redirect('/events')->with('success','Succesfully subscribe, you will get email for upcoming interesting events');
    }
}
