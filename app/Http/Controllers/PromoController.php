<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Promo;
use DB;

class PromoController extends Controller
{
    public function applyPromoCode($eventID){
        $data = array('success'=>'Successfully Apply Promotional Code','isPromoted'=>'true');
        return back()->with($data);
    }
}
