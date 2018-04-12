<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class StatisticsController extends Controller
{
    public function getAttendanceDataByCategory(){
    	header('Content-Type: application/json');
    	$data = DB::select(
    		'SELECT booking as expectedAttendance , (booking - absence) as realAttendance , categories.name as category from
			(select count(*) as booking , events.category
			from booking INNER JOIN events
			on booking.eventID = events.id
			GROUP BY events.category) A
			INNER JOIN
			(select count(*) as absence, events.category
			from absent INNER JOIN events
			on absent.eventID = events.id
			GROUP BY events.category) B
			ON A.category = B.category
			INNER JOIN categories
			ON B.category = categories.id'
    	);
    	echo json_encode($data);
    }
    public function getAttendanceDataByTime(){
    	header('Content-Type: application/json');
    	$currentDate = date("Y-m-d");
    	$beginningDate = strtotime('-6 months' , strtotime($currentDate));
    	$beginningDate = date("Y-m-d",$beginningDate);
    	$data = DB::select(
    		"SELECT booking as expectedAttendance , (booking - absence) as realAttendance , A.month from
			(SELECT count(*) as booking , MONTHNAME(time) as month
			from booking INNER JOIN events
			on booking.eventID = events.id
			WHERE events.time BETWEEN ? AND ?
			group by MONTHNAME(time)
			ORDER BY time DESC) A
			INNER JOIN
			(select count(*) as absence, MONTHNAME(time) as month
			from absent INNER JOIN events
			on absent.eventID = events.id
			WHERE events.time BETWEEN ? AND ?
			GROUP BY MONTHNAME(time)
			ORDER BY time DESC) B
			ON A.month = B.month",[$beginningDate,$currentDate,$beginningDate,$currentDate]
    	);
    	echo json_encode($data);
    }

    public function getAttendanceDataByUndergraduate(){
    	header('Content-Type: application/json');
    	$data = DB::select(
			"SELECT count(*) as attendance, categories.name FROM
			booking INNER JOIN events
			ON booking.eventID = events.id
			INNER JOIN users
			ON booking.userID = users.id
			INNER JOIN categories
			ON events.category = categories.id
			WHERE role = 2
			GROUP BY categories.name");
    	echo json_encode($data);
    }
    public function getAttendanceDataByPostgraduate(){
    	header('Content-Type: application/json');
    	$data = DB::select(
			"SELECT count(*) as attendance, categories.name FROM
			booking INNER JOIN events
			ON booking.eventID = events.id
			INNER JOIN users
			ON booking.userID = users.id
			INNER JOIN categories
			ON events.category = categories.id
			WHERE role = 3
			GROUP BY categories.name");
    	echo json_encode($data);
    }

    public function getProfitDataByCategory(){
    	header('Content-Type: application/json');
    	$data = DB::select(
			"SELECT SUM(total) as profit, categories.name as name, concat('#',SUBSTRING((lpad(hex(@curRow := @curRow + 10),6,0)),-6)) AS color 
			FROM buy INNER JOIN events
			ON buy.eventID = events.id
			INNER JOIN categories
			ON categories.id = events.category
			INNER JOIN (SELECT @curRow := 5426175) color_start_point
			GROUP BY categories.name");
    	echo json_encode($data);
    }

    public function getProfitDataByTime(){
    	header('Content-Type: application/json');
    	$currentDate = date("Y-m-d");
    	$beginningDate = strtotime('-6 months' , strtotime($currentDate));
    	$beginningDate = date("Y-m-d",$beginningDate);
    	$data = DB::select(
			"SELECT SUM(total) as profit, time as date
			FROM buy INNER JOIN events
			ON buy.eventID = events.id
			WHERE time BETWEEN ? AND ?
			GROUP BY time
			ORDER BY time ASC",[$beginningDate,$currentDate]);
    	echo json_encode($data);
    }
}
