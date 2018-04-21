<?php
namespace App\Library;
use MCordingley\LinearAlgebra\Matrix;
use DB;



class NeuralNetwork{
	protected $n_inputs, $n_hiddens, $n_outputs;
	protected $weights_ih, $weights_ho;
	protected $bias_ih, $bias_ho;
	protected $rate;
	public function __construct($n_inputs, $n_hiddens , $n_outputs , $learning_rate){
		$this->n_inputs = $n_inputs;
		$this->n_hiddens = $n_hiddens;
		$this->n_outputs = $n_outputs;
		$this->weights_ih = new Matrix($n_inputs,$n_hiddens);
		$this->weights_ho = new Matrix($n_hiddens, $n_outputs);
		$this->weights_ih->randomize();
		$this->weights_ho->randomize();
		$this->bias_ih = new Matrix(1,$n_hiddens);
		$this->bias_ho = new Matrix(1, $n_outputs);
		$this->bias_ih->randomize();
		$this->bias_ho->randomize();
		$this->rate = $learning_rate;
 	}

 	public static function load(){
 		$resultNN = DB::select('SELECT * FROM neuralnetwork');
 		$brain = new self($resultNN[0]->n_inputs,$resultNN[0]->n_hiddens,$resultNN[0]->n_outputs,$resultNN[0]->learning_rate);
 		$resultWeights = DB::select('SELECT * FROM nnweights');
 		foreach($resultWeights as $edge){
 			$start_node = $edge->start_node;
 			$start = substr($start_node,1);
 			$end = substr($edge->end_node,1);
 			if($start_node[0] == 'i'){
 				$brain->weights_ih->set($start,$end,$edge->weight);
 			}
 			else{
 				$brain->weights_ho->set($start,$end,$edge->weight);
 			}
 		}
 		$resultBias = DB::select('SELECT * FROM bias');
 		foreach($resultBias as $bias){
 			$layer = $bias->layer;
 			$end = substr($bias->end_node,1);
 			if($layer == 'i'){
 				$brain->bias_ih->set(0,$end,$bias->weight);
 			}
 			else{
 				$brain->bias_ho->set(0,$end,$bias->weight);
 			}
 		}
 		return $brain;
 	}
 	public function sigmoid($x){
 		return (1/(1+exp(-$x)));
		// return (2/(1+exp(-$x))) - 1;
	}

	public function dsigmoid($z){
		return $z*(1-$z);
		// return 1-$z*$z;
	}

 	public function feedForward($input_array){
		/*
		input_i * weights_ih = hidden

											|--	  w(h)11  ,   w(h)12   --| 	 	
	Sigmoid	 (|--i1	,  i2 ,   i3--|	 *** 	|	  w(h)21  ,   w(h)22     |  +  |--  b11  ,  b12 --|	)	=    |--  h1  , h2 --|
											|--	  w(h)31  ,   w(h)32   --|
	
		hidden * weights_ho = output

											|--	  w(o)11  ,   w(o)12   --|
	Sigmoid		(|-- h1, h2 --|	 ***	 	|--	  w(o)21  ,   w(o)22   --|   + |--  b21 , b22 --|  	)  =  |-- o1 , o2 --| 
	
		*/

		//Generate hidden's outputs
 		$inputs = Matrix::fromArray($input_array);
 		$hiddens = $inputs->multiplyMatrix($this->weights_ih);
 		$hiddens = $hiddens->addMatrix($this->bias_ih);
 		$hiddens->map([$this,'sigmoid']); 

 		//Generate output's outputs
 		$outputs = $hiddens->multiplyMatrix($this->weights_ho);
 		$outputs = $outputs->addMatrix($this->bias_ho);
 		$outputs = $outputs->map([$this,'sigmoid']);
 		return $outputs->to1DArray();
 	}

 	public function train($input_array,$target_array,$numberOfTrain){
 		for($a = 0 ; $a < $numberOfTrain; $a++){
 			$i = rand()%sizeof($input_array);
	 		//Generate hidden's outputs
	 		$inputs = Matrix::fromArray($input_array[$i]);
	 		$hiddens = $inputs->multiplyMatrix($this->weights_ih);
	 		$hiddens = $hiddens->addMatrix($this->bias_ih);
	 		$hiddens = $hiddens->map([$this,'sigmoid']);
	 		//Generate output's outputs
	 		$outputs = $hiddens->multiplyMatrix($this->weights_ho);
	 		$outputs = $outputs->addMatrix($this->bias_ho);
	 		$outputs = $outputs->map([$this,'sigmoid']);
	 		// $outputs->print();
	 		//Convert array to matrix object
	 		$targets = Matrix::fromArray($target_array[$i]);
	 		// $targets->print();
	 		// Find errors and improve weights in layer hidden-output
	 		$errors = $outputs->subtractMatrix($targets);
 			// echo "errors b4 :<br>";
 			// $errors->print();
	 		//cost^2 = summation (error ^ 2) * (1/2)
	 		//output = sigmoid(z), z = hiddens_i * w_ij
	 		//dcost_doutput = errors
	 		//doutput_dz = dsigmoid(outputs) where outputs = sigmoid(z)
	 		//Gradients = dcost_doutput * doutput_dz * dz_dw
	 		//Gradients = $errors * dsigmoid(output) * dz_dw
	 		$gradients = Matrix::mapTo(array($this,'dsigmoid'),$outputs);

	 		$gradients = $gradients->entrywise($errors);
 			$gradients = $gradients->multiplyScalar($this->rate);
	 		//Adjust bias in layer hidden-output
	 		$this->bias_ho = $this->bias_ho->subtractMatrix($gradients);
	 		//Adjust weights in layer hidden-output
	 		/*

				hidden_T =  	|--	 h1   --|
								|	 h2 	|
 								|--	 h3	  --|
				gradients =  [g1 , g2]

 				=> dcost_dw = hidden_T * gradients = 

 				|--		h1*g1    h1*g2		--|
 				|		h2*g1    h2*g2		  |
 				|--		h3*g1    h3*g2		--|
 	 		*/
 			$hiddens_T = $hiddens->transpose();
 			$gradients = $hiddens_T->multiplyMatrix($gradients);
 			$this->weights_ho = $this->weights_ho->subtractMatrix($gradients);

 			//Using back propagation to find errors_hidden and adjusting weights in layer input-hidden
 			/*
 			  					|--	 w11   w21 	 w31 	--|  
				[e1   e2]   *   |--  w12   w22   w32 	--|  = [eh1   eh2   eh3]

				contribution of node 1 to e1 + contribution of node 1 to e2 = eh1
				=> e1 * w11 + e1 * w21  = eh1
				=> errors * weights_ho_T = errors_h
 			*/
 			$weights_ho_T = $this->weights_ho->transpose();
 			$errors_h = $errors->multiplyMatrix($weights_ho_T);
 			//Let z = summation (input_i * w_ij) + b
 			//dcost_dhidden = errors_h
 			//dhidden_dz = dsigmoid(hidden) where hidden = sigmoid(z)
 			//Gradients_h = dcost_dhidden * dhidden_dz * dz_dw  = errors_h * dsigmoid(hiddens) * dz_dw
 			$gradients_h = Matrix::mapTo(array($this,'dsigmoid'),$hiddens);
 			$gradients_h = $gradients_h->entrywise($errors_h);
 			$gradients_h = $gradients_h->multiplyScalar($this->rate);
 			//Adjust bias_ih

 			$this->bias_ih = $this->bias_ih->subtractMatrix($gradients_h);
 			$inputs_T = $inputs->transpose();
 			$gradients_h = $inputs_T->multiplyMatrix($gradients_h);
 			//Adjust weights in layer input-hidden
 			$this->weights_ih = $this->weights_ih->subtractMatrix($gradients_h);
 			// $pred = Matrix::fromArray($this->feedForward($input_array[$i]));
 			// $pred->print();
 			// $errAfter = $pred->subtractMatrix($targets);
 			// echo "Error after <br>";
 			// $errAfter->print();
 		}
 	}

 	public function store($accuracy){
 		$rs = DB::table('neuralnetwork')->select('accuracy')->first();
 		if($rs == null || ($accuracy*0.96) > $rs->accuracy){
 			DB::table('neuralnetwork')->delete();
 			DB::table('nnweights')->delete();
 			DB::table('bias')->delete();
			DB::table('neuralnetwork')->insert(['n_inputs'=>$this->n_inputs,'n_hiddens'=>$this->n_hiddens,'n_outputs'=>$this->n_outputs,'accuracy'=>$accuracy , 'learning_rate'=>$this->rate]);
			//Save weights in input-hidden layer
			$weights_ih = $this->weights_ih->toArray();
			for($i = 0 ; $i < sizeof($weights_ih) ; $i++){
				$start_node = 'i'.$i;
				for($j = 0; $j < sizeof($weights_ih[0]); $j++){
					$end_node = 'h'.$j;
					DB::table('nnweights')->insert(['start_node'=>$start_node,'end_node'=>$end_node,'weight'=>$weights_ih[$i][$j]]);
				}
			}

			// Save bias in input-hidden layer
			$bias_ih = $this->bias_ih->to1DArray();
			for($i = 0 ; $i < sizeof($bias_ih); $i++){
				$end_node = 'h'.$i;
				DB::table('bias')->insert(['layer'=>'i','end_node'=>$end_node,'weight'=>$bias_ih[$i]]);
			}

			//Save weights in hidden-output layer
			$weights_ho = $this->weights_ho->toArray();
			for($i = 0 ; $i < sizeof($weights_ho) ; $i++){
				$start_node = 'h'.$i;
				for($j = 0; $j < sizeof($weights_ho[0]); $j++){
					$end_node = 'o'.$j;
					DB::table('nnweights')->insert(['start_node'=>$start_node,'end_node'=>$end_node,'weight'=>$weights_ho[$i][$j]]);
				}
			}

			// Save bias in hidden-output layer
			$bias_ho = $this->bias_ho->to1DArray();
			for($i = 0 ; $i < sizeof($bias_ho); $i++){
				$end_node = 'o'.$i;
				DB::table('bias')->insert(['layer'=>'h','end_node'=>$end_node,'weight'=>$bias_ho[$i]]);
			}
 		}
 	}

 

}

?>