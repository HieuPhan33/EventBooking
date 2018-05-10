<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bookmark;
use DB;
use Carbon\Carbon;

class BookmarkController extends Controller
{
    public function store(Request $request){
    	DB::table('bookmark')->insert(
    		['eventID' => $request->eventID, 'userID' => auth()->user()->id]
    	);
    	return "storing successfully";
    }

    public function remove(Request $request){
		DB::delete("DELETE FROM  bookmark WHERE eventID = ? AND userID = ?", [$request->eventID, auth()->user()->id]);
    	return 'removing successfully';

    }
}
