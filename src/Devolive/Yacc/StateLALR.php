<?php declare(strict_types=1);

namespace Devolive\Yacc;

class StateLALR {

	// Attribute
	private $grammar;
	private $prodReadeds;
	private $index;


	// Constructor
	private function __construct(Grammar &$grammar) {

		// Initialise attributes
		$this->grammar = $grammar;
		$this->prodReadeds = [];

	}

 	public static function generate(Grammar &$grammar) : void {

        $instance = new StateLALR($grammar);

		$firstProduction = $grammar->getProduction(0);
		$instance->prodReadeds = $firstProduction->read(0)->after();

		var_dump($instance->prodReadeds);
		die();
		
		$instance->index = $grammar->addState($instance) - 1;
		if ($instance->index != 0) {
			throw new Exception("Error generation first state", 1);
		}

		self::generateNextState($instance);
    }

	private static function generateNextState(StateLALR $previousState) {

		$instances = [];

		$changed = FALSE;
		for ($i = count($previousState->prodReadeds); --$i >= 0; ) {

			$tmp = $previousState->prodReadeds[$i]->next();

			print("<br/>========================================".$previousState->prodReadeds[$i]);
			print("<br/>----------------------------------------$tmp");
			if ($tmp != NULL) {
				var_dump($tmp->after());
				if (!isset($instances[$tmp->getPreviousSymbol()->getId()])) {
					$instances[$tmp->getPreviousSymbol()->getId()] = new StateLALR($previousState->grammar);
				}
				$instances[$tmp->getPreviousSymbol()->getId()]->prodReadeds = array_merge($instances[$tmp->getPreviousSymbol()->getId()]->prodReadeds, $tmp->after());
				//$instance->prodReadeds = array_merge($instance->prodReadeds, $tmp->after());
				$changed = TRUE;
			}
		}

		foreach ($instances as $key => $instance) {
			$instance->index = $previousState->grammar->addState($instance) - 1;	
		}
		if ($changed) {
			self::generateNextState($instance);
		}

	}


	private function afterRecursive(array $res) : array {

		for ($i = count($res); --$i >=0; ) {

			// Get the symbol on right
			$symbol = $res[$i]->getSymbol();
			// Check if the symbol is a rule
			if (!$symbol->isTerminal()) {

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


	// Method
	public function __toString() : string {

		$res = "";

		for ($i = 0; $i < count($this->prodReadeds); $i++) {
			$res .= "<br/>".$this->prodReadeds[$i];
		}

		return $res;
	}
}
