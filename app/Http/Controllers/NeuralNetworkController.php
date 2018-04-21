<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\NeuralNetwork;
use App\Library\Standardizer;
use Storage;
use Illuminate\Support\Facades\File;
use MCordingley\LinearAlgebra\Matrix;
use DB;
use Exception;
function sigmoid($x){
	return 1/(1+exp(-$x));
}
class NeuralNetworkController extends Controller
{
	public function test(){
		return view("test");
	}
    public function guessIt(Request $request){

    	$flowerDataText = 'app\flowerData.txt';
    	$flowerDataType = [0,0,0,0];
    	$carDataText = 'app\carData.txt';
    	$carDataType = [2,2,2,2,2,2];
    	$wineDataText = 'app\wineData.txt';
    	$wineDataType = [0,0,0,0,0,0,0,0,0,0,0,0,0];
    	$data = $this->generateDataFromText($carDataType,$carDataText);

    	$inputs = $data[0];
    	$targets = $data[1];
    	$standardInput = $data[2];
    	$standardOutput = $data[3];
    	$n_inputs = $data[4];
    	$n_outputs = $data[5];

    	// $brain = new NeuralNetwork( $n_inputs , 4 , $n_outputs , 0.1 );
    	$brain = new NeuralNetwork( $n_inputs , 3 , $n_outputs , 0.1 );
    	$brain->train($inputs,$targets,20000);
    	$count = 0;
    	for($a = 0; $a < 1000; $a++){
    		$i = rand()%sizeof($inputs);
    		$guessArr = $brain->feedForward($inputs[$i]);
    		$guess = $this->findMax($guessArr);
    		$correct = $this->findMax($targets[$i]);
    		// echo "Guess ".$guess." Expect ".$correct;
    		// echo "<br>";
    		if($guess == $correct)
    			$count++;
    	}
    	echo "Total number of right guess ".$count;
    	
    	/*
    	Handle input and give a guess for car
    	*/
    	// $mystery = array();
    	// $mystery[0] = $request->input('buying');
    	// $mystery[1] = $request->input('maint');
    	// $mystery[2] = $request->input('doors');
    	// $mystery[3] = $request->input('persons');
    	// $mystery[4] = $request->input('lug_boots');
    	// $mystery[5] = $request->input('safety');
    	// $normalizedMystery = $standardInput->normalizeFromData($mystery);
    	// $outputs = $brain->feedForward($normalizedMystery);
    	// $guess = $standardOutput->revertOutput($outputs);
    	// if(strcmp($guess,'unacc'))
    	// 	$guess = 'unacceptable';
    	// else if(strcmp($guess,'acc'))
    	// 	$guess = 'acceptable';
    	// return view('test')->with('guess',$guess);
    }

    public function trainNN(){
    	
    	$data = $this->generateData();
    	$inputs = $data[0];
    	$targets = $data[1];
    	$n_inputs = $data[2];
    	$n_outputs = $data[3];
    	$brain = new NeuralNetwork( $n_inputs , 2 , $n_outputs , 0.1 );
    	$brain->train($inputs,$targets,20000);
    	$count = 0;
    	for($a = 0; $a < 100; $a++){
    		$i = rand()%sizeof($inputs);
    		$guessArr = $brain->feedForward($inputs[$i]);
    		$guess = $this->findMax($guessArr);
    		$correct = $this->findMax($targets[$i]);
    		// echo "Guess ".$guess." Expect ".$correct;
    		// echo "<br>";
    		if($guess == $correct)
    			$count++;
    	}
    	echo "Total number of right guess ".$count;
    	$accuracy = $count / 100;
    	$brain->store($accuracy);
    }

    public function loadNN(){
    	$data = $this->generateData();
    	$inputs = $data[0];
    	$targets = $data[1];
    	$n_inputs = $data[2];
    	$n_outputs = $data[3];
    	$loadBrain = NeuralNetwork::load();
    	$count = 0;
    	for($a = 0; $a < 100; $a++){
    		$i = rand()%sizeof($inputs);
    		$guessArr = $loadBrain->feedForward($inputs[$i]);
    		$guess = $this->findMax($guessArr);
    		$correct = $this->findMax($targets[$i]);
    		// echo "Guess ".$guess." Expect ".$correct;
    		// echo "<br>";
    		if($guess == $correct)
    			$count++;
    	}
    	echo "Total number of right guess ".$count;
    }

    public function generateData(){
    	$data = array();
    	$targets = array();
    	$allStudents = DB::select('SELECT * from users where id IN (SELECT userID FROM booking)');
    	$i = 0;
    	$dataType = [0,1,1,2,2];
    	foreach($allStudents as $student){
    		$studentID = $student->id;
    		$cnt = 0;
			$data[$i] = array();
			$data[$i][$cnt] = $student->age;
			$cnt++;
			$data[$i][$cnt] = $student->sex;

			$cnt++;
			$data[$i][$cnt] = $student->studentType;
			$cnt++;
			$data[$i][$cnt] = $student->degree;
			$cnt++;
			$data[$i][$cnt] = $student->favoriteClubType;
			$cnt++;
			//Get output
			$targets[$i] = array();
    		$topPref = DB::select('
    			SELECT booking.userID , categories.id as category , count(*) as count
				FROM booking INNER JOIN events 
				ON booking.eventID = events.id
				INNER JOIN categories
				ON events.category = categories.id
				WHERE booking.userID = ?
				GROUP BY userID, categories.id
				ORDER BY count DESC
				LIMIT 1',[$studentID]);
    		$prefValue = $topPref[0]->category - 1;
    		$targets[$i][0] = $topPref[0]->category;
    		$i++;
    	}
    	$standardizerData = new Standardizer($data,$dataType);
    	$standardizerTargets = new Standardizer($targets,[3]);
    	$normalizedData = $standardizerData->normalizeData();
    	$normalizedTargets = $standardizerTargets->normalizeData();
    	// Return normalized data , normalized target , number of input_nodes of normalized data , number of output_nodes of normalized targets
    	return [$normalizedData , $normalizedTargets,
    			sizeof($normalizedData[0]) , $standardizerTargets->getTotalDistinctVal(0)];

    }




    public function generateDataFromText($dataType,$text){
    	$data = array();
    	$targets = array();
    	$outputIndex = sizeof($dataType);
    	$i = 0;
    	foreach(file(storage_path($text)) as $line){
    		$sample = explode(",",$line);
    		$data[$i] = array();
    		$targets[$i] = array();
    		for($j = 0 ; $j < sizeof($sample) ; $j++){
    			if($j == $outputIndex){
    				$targets[$i][0] = $sample[$j];
	    		}
	    		else{
    				$data[$i][$j] = $sample[$j];
    			}
    		}
    		$i++;
    	}
    	$standardizerData = new Standardizer($data,$dataType);
    	$standardizerTargets = new Standardizer($targets,[3]);
    	$normalizedData = $standardizerData->normalizeData();
    	$normalizedTargets = $standardizerTargets->normalizeData();
    	//Return standard to convert any new input data , normalized data , normalized target , number of input_nodes of normalized data , number of output_nodes of normalized targets
    	return [$normalizedData , $normalizedTargets, $standardizerData , $standardizerTargets, 
    			sizeof($normalizedData[0]) , $standardizerTargets->getTotalDistinctVal(0)];
   		
    }

    public function generateCarData(){
    	$data = array();
    	$targets = array();
    	$dataType = [2,2,2,2,2];
    	$i = 0;
    	foreach(file(storage_path('app\carData.txt')) as $line){
    		$sample = explode(",",$line);
    		$data[$i] = array();
    		$targets[$i] = array();
    		for($j = 0 ; $j < sizeof($sample) ; $j++){
    			if($j == 6){
    				$targets[$i][0] = $sample[$j];
	    		}
	    		else{
    				$data[$i][$j] = $sample[$j];
    			}
    		}
    		$i++;
	    	
    	}
    	$standardizerData = new Standardizer($data,$dataType);
    	$standardizerTargets = new Standardizer($targets,[3]);
    	$normalizedData = $standardizerData->normalizeData();
        $normalizedData->store();
    	$normalizedTargets = $standardizerTargets->normalizeData();
    	//Return normalized data , normalized target , number of input_nodes of normalized data , number of output_nodes of normalized targets
    	return [$normalizedData , $normalizedTargets,
    			sizeof($normalizedData[0]) , $standardizerTargets->getTotalDistinctVal(0)];
    }

    public function findMax($array){
    	$index = 0;
    	$max = 0;
    	for($i = 0 ; $i < sizeof($array) ; $i++){
    		if($array[$i] > $max){
    			$max = $array[$i];
    			$index = $i;
    		}
    	}
    	return $index;
    }

    public function editText(){
    	// $rs = array();
    	// $i = 0;
    	// $str = '';
    	// foreach(file(storage_path('app\wineData.txt')) as $line){
    	// 	$rs[$i] = array();
    	// 	$data = explode(",",$line);
    	// 	for($j = 1 ; $j <= sizeof($data) ; $j++){
    	// 		if($j != sizeof($data)){
    	// 			$str = $str.trim($data[$j]).',';
    	// 		}
    	// 		else{
    	// 			$str = $str.$data[0]."\n";
    	// 		}
    	// 	}
    	// 	$i++;
    	// }
    	// File::put(storage_path('app\wineData.txt'), $str);
    }


}

