<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Library\NeuralNetwork;
use App\Library\Standardizer;
use App\Event;
use App\PromoCode;
use Storage;
use DB;
use Carbon\Carbon;

class EventsController extends MailController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //If user is an event host , only display events under his responsibility
        if(auth()->user()->role == 1){
            $events = DB::select(
                'SELECT title, price, categories.name as category, description , slotsLeft, events.id as id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time 
                 FROM events INNER JOIN categories
                ON events.category = categories.id
                 WHERE time >= ? AND hostID = ? AND isFinalized = false
                ORDER BY time ASC',[date("Y-m-d H:i:s", time()-60*60*24),auth()->user()->id]);
        }
        //When user is manager
        else if(auth()->user()->role == 0){
            $events = DB::select(
                'SELECT title,price, categories.name as category, description , slotsLeft, events.id as id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time
                 FROM events INNER JOIN categories
                ON events.category = categories.id
                 WHERE time >= ?
                ORDER BY time ASC',[ date("Y-m-d H:i:s")]);
        }
        //When user is student
        else{
            $topPref = $this->getTopPreference();
            if($topPref == 0){
                $topPref = $this->predictUserPreference();
            }
            $orderedCategoryList = $this->getCategoryListByPreference($topPref);
            $categoryOrderStr='';
            foreach($orderedCategoryList as $category){
                $categoryOrderStr = $categoryOrderStr.','.$category;
            }
            $events = DB::select(
                'SELECT title, price, categories.name as category, description , slotsLeft, events.id as id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time , 
                IF(events.id IN(
                    SELECT eventID FROM booking WHERE userID = ?)
                ,true,false) AS isBooked,
                IF(events.id IN(
                    SELECT eventID FROM bookmark WHERE userID = ?)
                ,true,false) AS isBookmarked,
                IF(events.id IN(
                    SELECT eventID FROM queue WHERE userID = ?)
                ,true,false) AS isEnqueued
                 FROM events INNER JOIN categories
                ON events.category = categories.id
                 WHERE time >= ?
                ORDER BY field(events.category'.$categoryOrderStr."), time ASC",[auth()->user()->id,auth()->user()->id,auth()->user()->id, date("Y-m-d H:i:s")]);

        }
        echo "<script>console.log('The top preference is ".$events[0]->category."')</script>";
        $entries = $this->arrayPaginator($events,$request);
        return view('pages.index')->with('events',$entries);
    }

    public function getTopPreference(){
        $topPref = 0;
        $rs = DB::select(
            'SELECT category , count(*) as count
            FROM booking INNER JOIN events 
            ON booking.eventID = events.id
            WHERE userID = ?
            GROUP BY userID, category
            ORDER BY count DESC
            LIMIT 1',[auth()->user()->id]);
        if(count($rs) > 0)
            $topPref = $rs[0]->category;
        return $topPref;
    }

    public function getCategoryListByPreference($rootCategory){
        $queue = new \Ds\Queue();
        $queue->push($rootCategory);
        $visited = array();
        array_push($visited , $rootCategory);
        $orderedCategoryList = array();
        while(!$queue->isEmpty()){
            $currentNode = $queue->pop();
            array_push($orderedCategoryList, $currentNode);
            //Select all categories having relationship with current category node
            $subRs = DB::select(
                    'SELECT categoryID2 FROM categoryRelationship
                    WHERE categoryID1 = ?',[$currentNode]
            );
            //Loop through each related category node
            foreach($subRs as $relatedNode){
                $relatedCategory = $relatedNode->categoryID2;
                //If we encounter a new category node , add to the queue
                if (!in_array($relatedCategory, $visited)){
                    $queue->push($relatedCategory);
                    //Mark as visited
                    array_push($visited, $relatedCategory);
                }
            }
        }
        return $orderedCategoryList;
    }

    public function predictUserPreference(){
        $result = DB::select('SELECT age, sex, studentType, degree, favoriteClubType FROM users WHERE id = ?',[auth()->user()->id]);
        // Display console for demo purpose
        echo '<script> console.log('.json_encode($result[0]).')</script>';
        $input = [$result[0]->age, $result[0]->sex, $result[0]->studentType, $result[0]->degree, $result[0]->favoriteClubType];
        $data_standardizer = Standardizer::load();
        $normalized_input = $data_standardizer->normalizeInput($input);
        $brain = NeuralNetwork::load();
        $guess = $brain->feedForward($normalized_input);
        $result = $data_standardizer->revertOutput($guess);
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Pass along the list of hosts and categories
        $hosts = DB::select(
            'SELECT id , name FROM users WHERE role = 1'
        );
        $categories = DB::select(
            'SELECT id,name FROM categories');
        return view('events.create')->with('hosts',$hosts)->with('categories',$categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'description'=>'required',
            'location'=>'required',
            'capacity'=>'required|integer',
            'datetime'=>'required',
            'price'=>'integer|nullable'
        ]);
        $date = $request->input('datetime');
        $hr =$request->input('hour');
        $minute = $request->input('minute');
        if ($request->input('type') == 1)
            $hr += 12;
        $hr %= 24;
        $datetime = $date." ".$hr.":".$minute.":00";
        $event = new Event;
        $event->title = $request->input('title');
        $event->time = $datetime;
        $event->location = $request->input('location');
        $event->description = $request->input('description');
        $event->category = $request->input('category');
        $event->hostID = $request->input('host');
        $event->capacity = $request->input('capacity');
        $event->slotsLeft = $request->input('capacity');
        $price = $request->input('price');
        if($price == null){
            $price = 0;
        }
        $event->price = $price;
        $event->save();
        $eventID = DB::getPdo()->lastInsertId();
        // Send events to whom interested
        $this->sendEventToUsers($eventID,$event->category);
        $numOfCodes = $request->input('numOfCodes');
        if($numOfCodes != 0){
            for($i = 1; $i <= $numOfCodes; $i++){
                $quantityID = 'quantity'.$i;
                $typeID = 'type'.$i;
                $this->validate($request,[
                    $quantityID=>'nullable|integer'
                ]);
                $quantity = $request->input($quantityID);
                $type = $request->input($typeID);
                for($j = 1; $j <= $quantity; $j++){
                    $promoCode = new PromoCode;
                    $promoCode->id = $this->getRandomString();
                    $promoCode->eventID = $eventID; 
                    $promoCode->type = $type;
                    $promoCode->save();
                }
            }
            //Send promotional code to the manager
            $this->sendPromoCode($eventID);
        }
        $dt = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'created event '.$event->title, 'timestamp'=>$dt->toDateTimeString()]);

        return redirect('/events')->with('success','Event Created');
    }
    // Send new events information to whom interested 
    public function sendEventToUsers($eventID,$category){
        $emails = DB::select('
            SELECT email FROM subscribe INNER JOIN users
            ON subscribe.userID = users.id
            WHERE categoryID = ?',[$category]);
        $this->informUsers($eventID,$emails);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function showToManager($id){
        if(auth()->user()->role  != 0){
            return redirect('/');
        }
        $event = DB::select(
            'SELECT title, price, categories.name as category, description , slotsLeft, events.id as id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time, promo_codes.id as isPromoted 
            FROM events INNER JOIN categories
            ON events.category = categories.id
            LEFT JOIN promo_codes
            ON events.id = promo_codes.eventID
            WHERE events.id = ?',[$id]
        );
        return view('events.managerShow')->with('event',$event[0]);
    }

    public function showToHost($id){
        if(auth()->user()->role != 1){
            return redirect('/');
        }
        $users = DB::select(
            'SELECT id, users.name , users.email, eventID
            FROM users JOIN booking
            ON users.id = booking.userID
            WHERE eventID = ?',[$id]
        );
        return view('events.hostShow')->with('users',$users);
    }

    public function showToStudent($id){
        if(auth()->user()->role != 2 && auth()->user()->role != 3){
            return redirect('/');
        }
        $event = DB::select(
            'SELECT title, price , categories.name as category, description , slotsLeft, events.id as id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time , IF(events.id IN(
                SELECT eventID FROM booking WHERE userID = ?)
            ,true,false) AS isBooked,
            IF(events.id IN(
                SELECT eventID FROM bookmark WHERE userID = ?)
            ,true,false) AS isBookmarked,
            IF(events.id IN(
                SELECT eventID FROM queue WHERE userID = ?)
            ,true,false) AS isEnqueued
            FROM events INNER JOIN categories
            ON events.category = categories.id
             WHERE events.id = ? AND time >= ?',[auth()->user()->id , auth()->user()->id, auth()->user()->id, $id , date("Y-m-d H:i:s")]);
        $promoCodes = DB::select(
            'SELECT id, type FROM promo_codes 
            WHERE eventID = ?',[$id]);
        $data = array("event"=>$event[0]);
        $codes = array();
        if(count($promoCodes) > 0){
            foreach($promoCodes as $promoCode){
                $codes[$promoCode->id] = $promoCode->type;
            }
            $codes = json_encode($codes);
            $data['codes'] = $codes;
        }
        return view('events.studentShow')->with($data);
    }

    public function showStudentDashboard(){
        $bookedEvents = DB::select(
            'SELECT events.id, title, DATE_FORMAT(time,"%Y-%m-%d") AS time 
            FROM events INNER JOIN booking
            ON events.id = booking.eventID
            WHERE time >= ? AND booking.userID = ?',[date("Y-m-d H:i:s"), auth()->user()->id]);
        $bookmarkedEvents = DB::select(
            'SELECT events.id, title, DATE_FORMAT(time,"%Y-%m-%d") AS time 
            FROM events INNER JOIN bookmark
            ON events.id = bookmark.eventID
            WHERE time >= ? AND bookmark.userID = ?',[date("Y-m-d H:i:s"), auth()->user()->id]);
        $data=['bookedEvents'=>$bookedEvents, 'bookmarkedEvents' => $bookmarkedEvents];
        return view('studentDashboard')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = DB::select('SELECT id,description,title, location, DATE_FORMAT(time,"%Y-%m-%d %H:%i") AS time , capacity, price FROM events WHERE id = ?',[$id]);
        $dt = new Carbon($event[0]->time);
        $date =  $dt->toDateString();
        $hr = sprintf("%02d",$dt->hour);
        $minute = sprintf("%02d",$dt->minute);
        //Pass along the list of hosts and categories
        $hosts = DB::select(
            'SELECT id , name FROM users WHERE role = 1'
        );
        $categories = DB::select(
            'SELECT id,name FROM categories');
        $data=['event'=>$event[0], 'hosts'=>$hosts, 'categories'=>$categories,'date'=>$date,'hour'=>$hr,'minute'=>$minute];
        return view('events.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title'=>'required',
            'description'=>'required',
            'location'=>'required',
            'capacity'=>'required|integer',
            'datetime'=>'required',
            'price'=>'integer|nullable'
        ]);
        $date = $request->input('datetime');
        $hr =$request->input('hour');
        $minute = $request->input('minute');
        if ($request->input('type') == 1)
            $hr += 12;
        $hr %= 24;
        $datetime = $date." ".$hr.":".$minute.":00";
        $event = Event::find($id);
        $event->title = $request->input('title');
        $event->time = $datetime;
        $event->location = $request->input('location');
        $event->description = $request->input('description');
        $event->category = $request->input('category');
        $event->hostID = $request->input('host');
        $event->capacity = $request->input('capacity');
        $event->slotsLeft = $request->input('capacity');
        $price = $request->input('price_edit');
        if($price == null){
            $price = 0;
        }
        $event->price = $price;
        $event->save();
        $numOfCodes = $request->input('numOfCodes');
        if($numOfCodes != 0){
            //Clear old promocodes
            DB::table('promo_codes')->where('eventID','=',$id)->delete();
            for($i = 1; $i <= $numOfCodes; $i++){
                $quantityID = 'quantity'.$i;
                $typeID = 'type'.$i;
                $this->validate($request,[
                    $quantityID=>'nullable|integer'
                ]);
                $quantity = $request->input($quantityID);
                $type = $request->input($typeID);
                if($quantity > 0){
                    for($j = 1; $j <= $quantity; $j++){
                        $promoCode = new PromoCode;
                        $promoCode->id = $this->getRandomString();
                        $promoCode->eventID = $id; 
                        $promoCode->type = $type;
                        $promoCode->save();
                    }
                }
            }
            //Send promotional code to the manager
            $this->sendPromoCode($id);
        }
        $dt = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'updated event '.$event->title, 'timestamp'=>$dt->toDateTimeString()]);
        return redirect('/events/'.$id.'/manager')->with('success','Event Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = DB::select("SELECT title from events where id = ?",[$id]);
        $date = new Carbon();
        DB::table('logs')->insert(['userID'=>auth()->user()->id, 'activity'=>'deleted event '.$event[0]->title, 'timestamp'=>$date->toDateTimeString()]);
        DB::table('booking')->where('eventID','=',$id)->delete();
        DB::table('bookmark')->where('eventID','=',$id)->delete();
        DB::table('buy')->where('eventID','=',$id)->delete();
        DB::table('promo_codes')->where('eventID','=',$id)->delete();
        DB::table('absent')->where('eventID','=',$id)->delete();
        DB::table('events')->where('id','=',$id)->delete();


        return redirect('/events')->with('success','Event Deleted');
    }
    public function search(Request $request)
    {

        $sql = 'SELECT title, price, description , slotsLeft, id, location ,  DATE_FORMAT(time, "%W %e %M %Y %H:%i") as time, 
                IF(id IN(
                    SELECT eventID FROM booking WHERE userID = '.auth()->user()->id.')
                ,true,false) AS isBooked,
                IF(id IN(
                    SELECT eventID FROM bookmark WHERE userID = '.auth()->user()->id.')
                ,true,false) AS isBookmarked,
                IF(id IN(
                    SELECT eventID FROM queue WHERE userID = '.auth()->user()->id.")
                ,true,false) AS isEnqueued
                FROM events
                 WHERE time >= '".date("Y-m-d H:i:s")."'";
        if(auth()->user()->role == 1)
            $sql = $sql.' AND isFinalized = false';
        //If users enter keywords
        if($request->input('keywords') != null){
            $sql = $sql.' AND (';
            $str = $request->input('keywords');
            //Split string by whitespace
            $keywords = preg_split('/\s+/',$str);
            //Find events having description or title that contains one or more keywords
            $i = 0;
            foreach ($keywords as $keyword){
                if($i == 0)
                    $sql= $sql." description LIKE '%".$keyword."%' OR title LIKE '%".$keyword."%'";
                else
                    $sql =  $sql." OR description LIKE '%".$keyword."%' OR title LIKE '%".$keyword."%'";
                $i++;
            }
            $sql = $sql.")";
            //If users filter by category , add category condition
            if($request->input('selectCat') != null){
                $sql = $sql.' AND category = '.$request->input('selectCat');
            }
        }
        //If users do not enter keywords, but filter by category
        else if($request->input('selectCat') != null){
            //Find events which have that category
            $sql = $sql. ' AND category = '.$request->input('selectCat');
        }
        $sql = $sql.' ORDER BY time ASC';
        $events = DB::select($sql);
        $entries = $this->arrayPaginator($events,$request);
        return view('pages.index')->with('events',$entries);
    }

    public function listPromoCodes(Request $request){
        $eventID = $_REQUEST['eventID'];
        $promoCodes = DB::select('
            SELECT id,type FROM promo_codes
            WHERE eventID = ?
            ORDER BY type DESC',[$eventID]);
        $promoCodes = json_encode($promoCodes);
        return $promoCodes;
    }

    protected function arrayPaginator($array, $request)
    {
        $page = Input::get('page', 1);
        $perPage = 2;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
    protected function getRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

}


