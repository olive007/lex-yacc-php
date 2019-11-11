<?php declare(strict_types=1);

namespace Devolive\Yacc\Grammar;

class Production {


	// Attribute
	private $index;
	private $symbolIds;
	private $action;
	private $rule;
	private $definition;
	private $lrItems;


	// Builder
	static public function build(string $expression, callable $action, Rule &$rule) : Production {

        $prod = new Production();

        $prod->symbolIds = explode(' ', $expression);
        $prod->action = $action;
        $prod->rule = $rule;
        $prod->definition = $rule->getName()." -> ".implode(" ", $prod->symbolIds);

        $prodIndex = $rule->getGrammar()->addProduction($prod) - 1;

        $prod->index = $prodIndex;

        // For each symbol we save them
        foreach ($prod->symbolIds as $symbol) {
            Symbol::build($symbol, $prod);
        }

        $prod->lrItems = Left2RightItem::build($prod);

		return $prod;
	}


	// Getter
	public function getIndex() : int {
		return $this->index;
	}

	public function getName() : string {
		return $this->rule->getName();
	}

	public function getGrammar() : Grammar {
		return $this->rule->getGrammar();
	}

	public function getDefinition() : string {
		return $this->definition;
	}

	public function getNbSymbol() : int {
		return count($this->symbolIds);
	}

	public function getSymbol(int $index) : ?Symbol {
		if (isset($this->symbolIds[$index])) {
			return $this->getGrammar()->getSymbol($this->symbolIds[$index]);
		}
		return NULL;
	}

	public function getFirstLrItem() : Left2RightItem {
	    return $this->lrItems[0];
    }


	// Method
	public function __toString() : string {
		return "($this->index) $this->definition";
	}

}
