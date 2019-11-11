<?php declare(strict_types=1);

namespace Devolive\Yacc\Grammar;

class Rule extends Symbol {


	// Attribute
	private $productionIndexes;


	// Constructor and builder
	protected function __construct(string $name, Grammar &$grammar) {

		// Call parent constructor
		parent::__construct($name, $grammar);

		// Initialise attributes
		$this->productionIndexes = [];

	}

	static public function define(string $name, array $expressions, array $actions, Grammar &$grammar) {

		$rule = new Rule($name, $grammar);

		// For each action
		for ($i = count($actions); --$i >= 0; ) {
			// Create the Production
			$prod = Production::build($expressions[$i], $actions[$i], $rule);
			// Save the production index
			array_push($rule->productionIndexes, $prod->getIndex());
		}

		$grammar->addSymbol($rule);

		return $rule;
	}


	// Getter
	public function isTerminal() : bool {
		return FALSE;
	}

	public function getNbProduction() : int {
		return count($this->productionIndexes);
	}

	public function getProduction(int $index) : Production {
        return $this->getGrammar()->getProduction($this->productionIndexes[$index]);
	}

	public function getFirstSymbol(array $res = [], array $checked = []) {

	    if (in_array($this, $checked)) {
	        return $res;
        }

	    array_push($checked, $this);

	    for ($i = $this->getNbProduction(); --$i >= 0; ) {
	        $firstSymbol = $this->getProduction($i)->getSymbol(0);
	        if (!$firstSymbol->isTerminal()) {
                $tmp = $firstSymbol->getFirstSymbol($res, $checked);
                $res = array_merge(array_diff($tmp, $res), $res);
            } else if (!in_array($firstSymbol, $res)) {
	            array_push($res, $firstSymbol);
            }
        }

	    return $res;

    }

}
