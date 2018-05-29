<?php declare(strict_types=1);

namespace Devolive\Yacc;

class TableLALR {

	// Attribute
	private $grammar;
	private $firstSymbols;
	private $followingSymbols;


	// Constructor
	public function __construct(Grammar $grammar) {
		

		// Initialise attribute
		$this->grammar = $grammar;
		$this->firstSymbols = $this->computeFirstSymbols($grammar);
		$this->followingSymbols = $this->computeFolowingSymbols($grammar);

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


		$res .= "\n\nFollowing Symbols";

		foreach ($this->followingSymbols as $key => $value) {
			$res .= sprintf("\n%-20s: [", $key);

			foreach ($value as $v) {
				$res .= "'".$v->getId()."', ";
			}
			$res = substr($res, 0, -2)."]";
		}

		return $res."\n";
	}

	private function computeFirstSymbols($grammar) {
		
		$res = [];

		foreach ($grammar->getSymbolKeys() as $key) {
			if ($grammar->getSymbol($key)->isTerminal()) {
				$res[$key] = [$grammar->getSymbol($key)];
			}
			else {
				$res[$key] = [];
			}
		}

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

		return $res;
	}

	private function computeFolowingSymbols($grammar) {

		$res = [];

		foreach ($grammar->getNoTerminalSymbolKeys() as $key) {
			$res[$key] = [];
		}
		$res[$grammar->getStartingRuleName()] = [$grammar->getSymbol('$end')];

		while (TRUE) {
			$changed = FALSE;

			for ($i = $grammar->getNbrProduction(); --$i >= 1; ) {
				$prod = $grammar->getProduction($i);
				for ($j = $prod->getNbrSymbol(); --$j >= 0; ) {
					$sym = $prod->getSymbol($j);
					if (!$sym->isTerminal()) {
						if ($prod->getNbrSymbol() > $j + 1) {
							$first = $this->firstSymbols[$prod->getSymbol($j + 1)->getId()];
						}
						else {
							$first = [];
						}
						foreach ($first as $tmp) {
							if (!in_array($tmp, $res[$sym->getName()])) {
								array_push($res[$sym->getName()], $tmp);
								$changed = TRUE;
							}
						}
						if (count($first) == 0 || $j == $prod->getNbrSymbol() - 1) {
							foreach ($res[$prod->getName()] as $tmp) {
								if (!in_array($tmp, $res[$sym->getId()])) {
									array_push($res[$sym->getId()], $tmp);
									$changed = TRUE;
								}
							}
						}
					}
				}
			}
			if (!$changed) {
				break;
			}
		}

		return $res;
	}

}
