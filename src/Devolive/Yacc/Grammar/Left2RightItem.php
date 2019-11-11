<?php declare(strict_types=1);

namespace Devolive\Yacc\Grammar;

class Left2RightItem {


	// Attribute
	private $production;
	private $readingIndex;


	// Constructor
	private function __construct(Production $production, int $readingIndex) {

		$this->production = $production;
		$this->readingIndex = $readingIndex;

	}

	// Constructor
	static public function build(Production $production) : array {

	    $items = [];

	    for ($i = $production->getNbSymbol(); --$i >= 0; ) {
            $items[$i] =  new Left2RightItem($production, $i);
        }

	    return $items;
	}


	// Getter
	public function getProduction() : Production {
		return $this->production;
	}

	public function getFollowingProductions() : array {
	    $res = [];

	    if (!$this->production->getSymbol($this->readingIndex)->isTerminal()) {
	        $rule = $this->getProduction()->getSymbol($this->readingIndex);
	        for ($i = $rule->getNbProduction(); --$i >= 0; ) {
	            array_push($res, $rule->getProduction($i));
            }
        }
	    return $res;
    }


	// Method
	public function __toString(): string {

		$res = $this->production->getName()." -> ";

		for ($i = 0; $i < $this->production->getNbSymbol(); $i++) {

			if ($i == $this->readingIndex) {
				$res .= ". ";
			}
			$res .= $this->production->getSymbol($i)->getName()." ";

		}
		if ($i == $this->readingIndex) {
			$res .= ". ";
		}


		return substr($res, 0, -1);
	}


}
