<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\NeuralNetwork;
use Storage;
use Illuminate\Support\Facades\File;
use MCordingley\LinearAlgebra\Matrix;
function sigmoid($x){
	return 1/(1+exp(-$x));
}
class NeuralNetworkController extends Controller
{
    public function test(){
    	$data = $this->generateData();
    	// for($i = 0; $i < sizeof($data) ;$i++ ){
    	// 	for($j = 0 ; $j < sizeof($data[$i]) ; $j++){
    	// 		echo $data[$i][$j]." -  ";
    	// 	}
    	// 	echo "<br>";
    	// }
    	$test = array();
    	$targets = array();
    	for($i = 0; $i < sizeof($data) ;$i++ ){
    		for($j = 0 ; $j < sizeof($data[$i])  ; $j++){
    			$targets[$i] = array_fill(0,3,0);
    			if($j == sizeof($data[$i]) - 1){
    				$targets[$i][$data[$i][$j]] = 1;
    			}
    			else{
    				$test[$i][$j] = $data[$i][$j];
    			}		
    		}
    	}

    	// for($i = 0; $i < sizeof($test) ;$i++ ){
    	// 	for($j = 0 ; $j < sizeof($test[$i]) ; $j++){
    	// 		echo $test[$i][$j]." -  ";
    	// 	}
    	// 	echo "<br>";
    	// 	print_r($targets[$i]);
    	// 	echo "<br>";
    	// }
    	$brain = new NeuralNetwork( 4 , 5 , 3 , 0.1);
    	$brain->train($test,$targets,10000);
    	$mystery = array();
    	for($i = 0; $i < sizeof($data) ;$i++ ){
    		for($j = 0 ; $j < sizeof($data[$i]) -1  ; $j++){
    			$mystery[$i][$j] = $data[$i][$j];
    		}		
    	}
    	$count = 0;
    	for($a = 0; $a < 30; $a++){
    		$i = rand()%sizeof($data);
    		$guessArr = $brain->feedForward($mystery[$i]);
    		print_r($guessArr);
    		$guess = $this->findMax($guessArr[0]);
    		echo "Guess ".$guess." Expect ".$data[$i][4];
    		echo "<br>";
    		if($guess == $data[$i][4])
    			$count++;
    	}
    	echo "Total number of right guess ".$count;
    	



    }

    public function generateData(){
    	$rs = array();
    	$i = 0;
    	foreach(file(storage_path('app\data.txt')) as $line){
    		$data = explode(",",$line);
    		for($j = 0 ; $j < sizeof($data) ; $j++){
    			if($j == 4){
    				$data[$j] = trim($data[$j]);
	    			if((strcmp($data[$j],'Iris-setosa')) == 0 ){
	    				$data[$j] = 0;
	    			}
	    			else if((strcmp($data[$j],'Iris-versicolor'))==0){
	    				$data[$j] = 1;
	    			}
	    			else
	    				$data[$j] = 2;
	    		}
    			$rs[$i][$j] = $data[$j];
    		}
    		$i++;
    	}
   		return $rs;


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


}

