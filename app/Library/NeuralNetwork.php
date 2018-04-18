<?php
namespace App\Library;
use MCordingley\LinearAlgebra\Matrix;



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
 	public function sigmoid($x){
		return 1/(1+exp(-$x));
	}

	public function dsigmoid($sigmoid){
		return $sigmoid*(1-$sigmoid);
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
 		return $outputs->toArray();
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
 			// $pred = Matrix::from2DArray($this->feedForward($input_array[$i]));
 			// $pred->print();
 			// $errAfter = $pred->subtractMatrix($targets);
 			// echo "Error after <br>";
 			// $errAfter->print();
 		}
 	}	

}

?>