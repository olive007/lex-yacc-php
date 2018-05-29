<?php declare(strict_types=1);

namespace Devolive\Yacc;

class TableLALR {

	// Attribute
	private $grammar;
	private $firstSymbols;
	private $folowingSymbols;


	// Constructor
	public function __construct(Grammar $grammar) {
		

		// Initialise attribute
		$this->grammar = $grammar;
		$this->firstSymbols = $this->computeFirstSymbols($grammar);
		//$this->folowingSymbols = $this->computeFolowingSymbols();

	}


	// Method
	public function __toString() : string {
		$res = "First Symbols";

		foreach ($this->firstSymbols as $key => $value) {
			$res .= sprintf("\n%-20s: [", $key);

			foreach ($value as $v) {
				$res .= $v->getId().", ";
			}
			$res = substr($res, 0, -2)."]";
		}

		return $res."\n";
	}

	private function computeFirstSymbols($grammar) {
		
		$res = [];

		// Terminal
		foreach ($grammar->getSymbolKeys() as $key) {
			if ($grammar->getSymbol($key)->isTerminal()) {
				$res[$key] = [$grammar->getSymbol($key)];
			}
			else {
				$res[$key] = [];
			}
		}
		//$this->firstSymbols['$end'] = ['$end'];

		while (TRUE) {
			$changed = FALSE;
			foreach ($grammar->getNoTerminalSymbolKeys() as $key) {
				for ($i = $grammar->getSymbol($key)->getNbrProduction(); --$i >= 0; ) {
					$prod = $grammar->getSymbol($key)->getProduction($i);

					foreach ($res[$prod->getSymbol(0)->getId()] as $sym) {
						if (!in_array($sym, $res[$key])) {
							array_push($res[$key], $sym);
							$changed = TRUE;
						}
					}
				}
			}

			if (!$changed) {
				break;
			}
		}
/*
		// No Terminal
		// Initialize the empty set
		for ($i = $this->grammar->getNbrNoTerminal(); --$i >= 0;) {
			$nTerm = $this->grammar->getNoTerminal($i);
			$this->firstSymbols[$nTerm] = [];
		}
		// Then propagate symbols until no change
		while (TRUE) {
			$changed = FALSE;
			for ($i = $this->grammar->getNbrNoTerminal(); --$i >= 0;) {
				$nTerm = $this->grammar->getNoTerminal($i);
				foreach ($this->grammar->getRulesByName($nTerm) as $rule) {
					foreach ($this->searchFirstNoTerminalSymbol($rule->getSymbols()) as $sym) {
						if (!in_array($sym, $this->firstSymbols[$nTerm])) {
							array_push($this->firstSymbols[$nTerm], $sym);
							$changed = TRUE;
						}
					}
				}
			}
			if (!$changed) {
				break;
			}
		}//*/

		return $res;

	}

	private function computeFolowingSymbols() {

		for ($i = $this->grammar->getNbrNoTerminal(); --$i >= 0; ) {
			$nTerm = $this->grammar->getNoTerminal($i);
			$this->folowingSymbols[$nTerm] = [];
		}

		$this->folowingSymbols[$this->grammar->getStartingRuleName()] = ['$end'];

		while (TRUE) {
			$folowingSymbolsAdded = FALSE;
			for ($i = 1; $i < $this->grammar->getNbrRule(); $i++) {
				$rule = $this->grammar->getRule($i);

				for ($j = $rule->getNbrSymbol(); --$j >= 0; ) {
					$sym = $rule->getSymbol($j);
					//print_r($sym."\n");
					if ($this->grammar->noTerminalPresent($sym)) {
						$fst = $this->searchFirstNoTerminalSymbol($rule->getSymbols($j+1));
						//print("\n\n".$rule->getName()."\n");
						//print_r($rule->getSymbols($j+1));
						//print_r($fst);
						$hasempty = FALSE;
						foreach ($fst as $s) {
							if ($s != '<empty>' && !in_array($s, $this->folowingSymbols[$sym])) {
								//print_r($this->folowingSymbols);
								array_push($this->folowingSymbols[$sym], $s);
								$folowingSymbolsAdded = TRUE;
							}
							if ($s == '<empty>') {
								$hasempty = TRUE;
							}
						}
						if ($hasempty || $i == $rule->getNbrSymbol() - 1) {
							//print("Toto:".$sym.":".$rule->getNbrSymbol()."\n");
							foreach ($this->folowingSymbols[$rule->getName()] as $s) {
								
								if (!in_array($s, $this->folowingSymbols[$sym])) {
									array_push($this->folowingSymbols[$sym], $s);
									$folowingSymbolsAdded = TRUE;
								}
							}
						}
					}
				}
			}
			if (!$folowingSymbolsAdded) {
				break;
			}
		}
	}

	private function searchFirstNoTerminalSymbol($symbols) : array {


		if (count($symbols) == 0) {
			return ["<empty>"];
		}

		$res = [];

		foreach ($symbols as $sym) {
			$x_produces_empty = FALSE;

			# Add all the non-<empty> symbols of First[x] to the result.
			foreach ($this->firstSymbols[$sym] as $s) {
				if ($s == '<empty>') {
					$x_produces_empty = TRUE;
				}
				else {
					if (!in_array($s, $res)) {
						array_push($res, $s);
					}
				}
			}
			if ($x_produces_empty) {
				continue;
			}
			else {
				break;
			}
		}
		print("----------------\n");
		print_r($res);
		return $res;
	}

}
