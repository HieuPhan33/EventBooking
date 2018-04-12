<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){

    	return view('pages.index');
    }

    public function service(){
    	$data = Array(
    		'title' => 'Services',
    		'services' => ['Web Designs','Programming', 'SQL']
    	);
    	return view('pages.services')->with($data);
    }

}
