<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Exception\ParserException;

class Production {


	// Attribute
	private $index;
	private $definition;
	private $symbolIds;
	private $action;
	private $rule;
	private $grammar;
	private $firstTerminals;


	// Constructor
	function __construct(string $expression, callable $action, Rule &$rule, Grammar &$grammar) {

		// Initialise attributes
		$this->symbolIds = [];
		$this->action = $action;
		$this->rule = $rule;
		$this->grammar = $grammar;
		$this->firstTerminals = [];


		// Split the string into an array
		$symbols = explode(' ', $expression);
		// Set the definition
		$this->definition = $rule->getName()." -> ".implode(" ", $symbols);

		$this->index = $grammar->addProduction($this) - 1;

		// For each all string
		foreach ($symbols as $s) {
			// Create a new symbol
			$symbol = new Symbol($s, $grammar);
			$grammar->getSymbol($s)->addAppearanceIndex($this->index);
			// Save the symbol id
			array_push($this->symbolIds, $s);
		}
		//$this->firstTerminals = array_unique(array_merge($this->firstTerminals, $grammar->getSymbol($symbols[0])->getFirstTerminals()));
	}


	// Getter
	public function getIndex() : int {
		return $this->index;
	}

	public function getDefinition() : string {
		return $this->definition;
	}

	public function getNbrSymbol() : int {
		return count($this->symbolIds);
	}

	public function getSymbol(int $index) : Symbol {
		return $this->grammar->getSymbol($this->symbolIds[$index]);
	}

	public function getFirstTerminals() : array {
		if ($this->firstTerminals == NULL) {
			$this->setFirstTerminals();
		}
		return $this->firstTerminals;
	}

	// Setter
	public function setFirstTerminals() {

		$sym = $this->getSymbol(0);

		if ($sym->isTerminal()) {
			$this->firstTerminals = [$sym];
		}
		else {
			$this->firstTerminals = [];// $this->grammar->getSymbol($sym->getId())->getFirstTerminals();
			/*while (TRUE) {
				$changed = FALSE;
				$tmp = $sym->getFirstTerminals();
				foreach ($tmp as $t) {
					if (!in_array($t, $this->firstTerminals)) {
						array_push($this->firstTerminals, $t);
						$changed = TRUE;
					}
				}
				if (!$changed) {
					break;
				}
			}*/
		}
	}

}
