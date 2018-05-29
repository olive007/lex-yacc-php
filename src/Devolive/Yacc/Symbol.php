<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Exception\ParserException;

class Symbol {


	// Attribute
	private $name;
	private $productionAppearance;
	protected $grammar;


	// Constructor
	function __construct(string $name, Grammar &$grammar) {

		//print($name."\n");
		// Initialise attributes
		$this->name = $name;
		$this->productionAppearance = [];
		$this->grammar = $grammar;

		$grammar->addSymbol($this);
	}


	// Getter
	public function getId() : string {
		return $this->name;
	}

	public function getName() : string {
		return $this->name;
	}

	public function getNbrAppearanceIndex() : int {
		return count($this->productionAppearance);
	}

	public function getAppearanceIndex(int $index) : int {
		return $this->productionAppearance[$index];
	}

	public function isTerminal() : bool {
		return TRUE;
	}

	public function getFirstTerminals() : array {
		return [$this];
	}


	// Method
	public function __toString() : string {
		$res = $this->name." ";
		$res .= $this->isTerminal() ? "(term)" : "(nTerm)";

		return $res;
	}

	public function addAppearanceIndex(int $productionIndex) {
		array_push($this->productionAppearance, $productionIndex);
	}

}
