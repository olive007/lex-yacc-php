<?php declare(strict_types=1);

namespace Devolive\Yacc;

class ProdReaded {


	// Attribute
	private $production;
	private $readingPosition;


	// Constructor
	function __construct(Production &$production, int $readingPosition) {

		// Initialise attributes
		$this->production = $production;
		$this->readingPosition = $readingPosition;

	}


	// Getter
	public function getReadingPosition() : int {
		return $this->readingPosition;
	}

	public function getSymbol() : ?Symbol {
		return $this->production->getSymbol($this->readingPosition);
	}

	public function getPreviousSymbol() : ?Symbol {
		return $this->production->getSymbol($this->readingPosition - 1);
	}

	public function next() : ? ProdReaded {
		return $this->production->read($this->readingPosition + 1);
	}


	// Method
	public function after() : array {

		return $this->afterRecursive([$this]);
	}

	private function afterRecursive(array $res) : array {

		for ($i = count($res); --$i >=0; ) {

			// Get the symbol on right
			$symbol = $res[$i]->getSymbol();
			// Check if the symbol is a rule
			if ($symbol != NULL && !$symbol->isTerminal()) {

				// In this case add all productions to the result
				for ($j = $symbol->getNbProduction(); --$j >= 0; ) {

					// Get the production one by one
					$prodRead = $symbol->getProduction($j)->read(0);
					// Check if the prod is already into the result table
					if (!$prodRead->isInArray($res)) {

						array_push($res, $prodRead);
						$tmp = $prodRead->afterRecursive($res);
						for ($k = count($tmp); --$k >= 0; ) {
							if (!$tmp[$k]->isInArray($res)) {
								array_push($res, $tmp[$k]);
							}
						}
					}
				}
			}
		}

		return $res;
	}

	private function isInArray(array $array) : bool {

		for ($i = count($array); --$i >= 0; ) {

			if ($this->readingPosition == $array[$i]->readingPosition &&
				$this->production->getIndex() == $array[$i]->production->getIndex()) {
				return TRUE;
			}

		}

		return FALSE;
	}


	// Method
	public function __toString() : string {
		$res = "(".$this->production->getIndex().")";
		$res .= " ".$this->production->getName()." -> ";


		$allRead = TRUE;
		for ($i = 0, $len = $this->production->getNbSymbol(); $i < $len; $i++) {
			if ($this->readingPosition == $i) {
				$res .= ". ";
				$allRead = FALSE;
			}
			$res .= $this->production->getSymbol($i)->getId()." ";
		}
		if ($allRead) {
			$res .= ". ";
		}
		return substr($res, 0, -1);
	}

}
