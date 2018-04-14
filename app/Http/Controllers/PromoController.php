<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Promo;
use DB;

class PromoController extends Controller
{
    public function applyPromoCode(Request $request, $eventID){
        $data = array('success'=>'Successfully Apply Promotional Code','isPromoted'=>'true','type'=>$request->input('type'),'code'=>$request->input('code'));
        return back()->with($data);
    }
}
