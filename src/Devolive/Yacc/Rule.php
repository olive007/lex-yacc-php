<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Exception\ParserException;

class Rule extends Symbol {


	// Attribute
	private $productionIndexes;


	// Constructor
	function __construct(string $name, array $expressions, array $actions, Grammar &$grammar) {

		// Call parent constructor
		parent::__construct($name, $grammar);

		// Initialise attributes
		$this->productionIndexes = [];

		// For each action
		for ($i = count($actions); --$i >= 0; ) {
			// Create the Production
			$prod = new Production($expressions[$i], $actions[$i], $this, $grammar);
			// Save the production index
			array_push($this->productionIndexes, $prod->getIndex());
		}

		//$grammar->addRule($this);
	}


	// Getter
	public function isTerminal() : bool {
		return FALSE;
	}

	public function getNbrProduction() : int {
		return count($this->productionIndexes);
	}

	public function getProduction(int $index) : Production {
		return $this->grammar->getProduction($this->productionIndexes[$index]);
	}


}
