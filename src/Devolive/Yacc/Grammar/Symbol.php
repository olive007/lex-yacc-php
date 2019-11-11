<?php declare(strict_types=1);

namespace Devolive\Yacc\Grammar;

class Symbol {


	// Attribute
	private $name;
	private $productionAppearance;
	private $grammar;


	// Constructor
	protected function __construct(string $name, Grammar &$grammar) {

		// Initialise attributes
		$this->name = $name;
		$this->productionAppearance = [];
		$this->grammar = $grammar;
	}

	static public function build(string $name, Production &$production) : Symbol {

	    $grammar = $production->getGrammar();

	    $symbol = $grammar->getSymbol($name);

		if ($symbol == NULL) {
            $symbol = new Symbol($name, $grammar);
			$grammar->addSymbol($symbol);
		}
		array_push($symbol->productionAppearance, $production->getIndex());

		return $symbol;
	}


	// Getter
    public function getName() : string {
		return $this->name;
	}

	public function getGrammar() : Grammar {
		return $this->grammar;
	}

	public function getNbAppearanceIndex() : int {
		return count($this->productionAppearance);
	}

	public function getAppearanceIndex(int $index) : int {
		return $this->productionAppearance[$index];
	}

	public function isTerminal() : bool {
		return TRUE;
	}


	// Method
	public function addAppearanceIndex(int $productionIndex) {
		array_push($this->productionAppearance, $productionIndex);
	}

	public function __toString() : string {
		$res = "Symbol";

		if ($this->isTerminal()) {
			$res .= "(Terminal): ";
		}
		else {
			$res .= "(No Terminal): ";
		}
		$res .= $this->name;

		return $res;
	}
}
