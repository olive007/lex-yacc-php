<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Exception\GrammarException;

class Grammar {

	// Attribute
	private $productions;
	private $symbols;
	private $productionDefinitions;
	private $startingRuleName;

	// Constructor
	public function __construct(\ReflectionClass $parser) {

		// Initialize attribute
		$this->symbols = [];
		$this->productions = [NULL];
		$this->productionDefinitions = [NULL];
		$this->startingRuleName = NULL;

		// For each all method of the child class
		foreach ($parser->getMethods() as $method) {

			// Use only the function which its name finish by Rule. !!! Case sensitive !!!
			if (preg_match("/Rule$/", $method->getName())) {

				// Set method accessible in case of the developer put the function as protected.
				$method->setAccessible(True);

				// Get the grammar form the document string and remove useless space
				$grammar = preg_replace('/^[\/\* \t]+/m', '', $method->getDocComment());

				if (!preg_match("/^\s*([a-z]+[a-z_0-9]*)\s*:\s*(.+)\s*$/m", $grammar, $matches)) {
					throw new GrammarException("Error in grammar");
				}

				// Save the name of the rule into tmp variable
				$name = $matches[1];
				if ($name == "error") {
					throw new GrammarException("Illegal rule name. 'error' is a reserved word");
				}

				// Save the first expressions
				$expressions = [$matches[2]];

				// Search for optional other expressions.
				if (preg_match_all("/^\|\s*(.*)\s*$/m", $grammar, $matches)) {
					$expressions = array_merge($expressions, $matches[1]);
				}

				// Invoke the method to get the callable
				$actions = $method->invoke(NULL);
				// Create the rule
				$rule = new Rule($name, $expressions, $actions, $this);

				// Set the first method
				if ($method->getName() == "firstRule") {
					$this->startingRuleName = $name;
				}
			}
		}
		if ($this->startingRuleName == NULL) {
			throw new GrammarException("No starting rule defined");
		}
	}


	// Getter
	public function getNbrProduction() : int {
		return count($this->productions);
	}

	public function getProduction(int $index) : Production {
		return $this->productions[$index];
	}

	public function getSymbolKeys() : array {
		return array_keys($this->symbols);
	}

	public function getNoTerminalSymbolKeys() : array {
		$res = [];

		foreach ($this->symbols as $key => $symbol) {
			if (!$symbol->isTerminal()) {
				array_push($res, $key);
			}
		}

		return $res;
	}

	public function getSymbol(string $id) : Symbol {
		return $this->symbols[$id];
	}

	public function getStartingRuleName() : string {
		return $this->startingRuleName;
	}


	// Setter


	// Method
	public function __toString() : string {

		$res = "Rules";

		for ($i = 0, $len = count($this->productionDefinitions); $i < $len ; $i++) {
			$res .= sprintf(" \n%-3d: %s", $i, $this->productionDefinitions[$i]);
		}

		$getTerm = function($termOrNot) {
			$res = "";
			foreach ($this->symbols as $symbol) {
				if ($symbol->isTerminal() == $termOrNot) {
					$res .= sprintf("\n%-20s: [", $symbol->getId());
					for ($i = 0, $len = $symbol->getNbrAppearanceIndex(); $i < $len; $i++) {
						$res .= $symbol->getAppearanceIndex($i)." ";
					}
					$res = substr($res, 0, -1)."]";
				}
			}
			return $res;
		};
		
		$res .= "\n\nTerminals";
		$res .= $getTerm(TRUE);
		$res .= "\n\nTerminals";
		$res .= $getTerm(FALSE);

		return $res."\n";
	}

	public function addProduction(Production $production) : int {

		// Get the definition of the production
		$prodDef = $production->getDefinition();

		// Check if the production is already defined
		if (in_array($prodDef, $this->productionDefinitions)) {
			throw new GrammarException("Illegal production: '".$prodDef."' Duplicated production");
		}
		// Save the production defintion
		array_push($this->productionDefinitions, $prodDef);

		// Save the production and the return the number of production saved
		return array_push($this->productions, $production);
	}

	public function addSymbol(Symbol $symbol) {

		// Check if the symbol is already defined
		if (!array_key_exists($symbol->getId(), $this->symbols)) {
			// Save the symbol into the symbol map attribute
			$this->symbols[$symbol->getId()] = $symbol;
		}
		// Check if the symbol is a Rule
		else if (!$symbol->isTerminal()) {
			// Check duplicate rule
			if (!$this->symbols[$symbol->getId()]->isTerminal()) {
				throw new GrammarException("Illegal rule: '".$symbol->getName()."' Duplicated rule");
			}
			// Get back the production appearance from the old symbol
			for ($i = $this->symbols[$symbol->getId()]->getNbrAppearanceIndex(); --$i >= 0; ) {
				$symbol->addAppearanceIndex($this->symbols[$symbol->getId()]->getAppearanceIndex($i));
			}
			// Replace the symbol by the rule
			$this->symbols[$symbol->getId()] = $symbol;
		}
	}

}
