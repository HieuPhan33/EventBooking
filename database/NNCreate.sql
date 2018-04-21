CREATE TABLE NNWeights(
	start_node CHAR(3),
	end_node CHAR(3),
	weight DECIMAL(28,25),
	PRIMARY KEY(start_node, end_node)
);

CREATE TABLE bias(
	layer CHAR(1),
	end_node CHAR(3),
	weight DECIMAL(28,25),
	PRIMARY KEY(layer, end_node)
);

CREATE TABLE NeuralNetwork(
	n_inputs INTEGER(2),
	n_hiddens INTEGER(2),
	n_outputs INTEGER(2),
	accuracy DECIMAL(5,5),
	learning_rate DECIMAL(5,5)
);