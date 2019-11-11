<?php declare(strict_types=1);

namespace Devolive\Yacc\Grammar;

use Devolive\Exception\GrammarException;

class Grammar {

    // Attribute
    private $productions;
    private $symbols;
    private $productionDefinitions;
    private $firstSymbols;
    private $followingSymbols;
    private $states;

    // Constructor
    public function __construct(\ReflectionClass $parser) {

        // Initialize attribute
        $this->symbols = [];
        $this->productions = [NULL];
        $this->productionDefinitions = [NULL];
        $this->states = [];

        // For each all method of the child class
        foreach ($parser->getMethods() as $method) {

            // Use only the function which its name finish by Rule. !!! Case sensitive !!!
            if (preg_match("/Rule$/", $method->getName())) {

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

                // Set method accessible in case of the developer put the function as protected.
                $method->setAccessible(True);

                // Invoke the method to get the callable
                $actions = $method->invoke(NULL);
                // Create the rule
                Rule::define($name, $expressions, $actions, $this);

                // Set the first method
                if ($method->getName() == "firstRule") {
                    $this->setStartingRuleName($name);
                }
            }
        }
        if ($this->productions[0] == NULL) {
            throw new GrammarException("No starting rule defined");
        }

        print($this);

        $this->generateFirstSymbols();
        $this->generateFollowingSymbols();
        $this->generateTable();
        print('Working unit HERE!!!!!!!!!!!');
        die();
    }


    // Getter
    public function getSymbol(string $id): ?Symbol {
        return isset($this->symbols[$id]) ? $this->symbols[$id] : NULL;
    }

    public function getNbProduction(): int {
        return count($this->productions);
    }

    public function getProduction(int $index): Production {
        return $this->productions[$index];
    }

    public function getSymbolKeys(): array {
        return array_keys($this->symbols);
    }

    public function getNoTerminalSymbolKeys(): array {
        $res = [];

        foreach ($this->symbols as $key => $symbol) {
            if (!$symbol->isTerminal()) {
                array_push($res, $key);
            }
        }

        return $res;
    }

    public function getNoTerminal() {

        foreach ($this->symbols as $symbol) {
            if (!$symbol->isTerminal()) {
                yield $symbol;
            }
        }

    }


    // Setter
    private function setStartingRuleName(string $name) {
        Rule::define("S'", [$name . ' $end'], [function () {
            return [];
        }], $this);
    }


    // Method
    public function __toString(): string {


        $printTerminal = function ($termOrNot) {
            $res = "";
            foreach ($this->symbols as $symbol) {
                if ($symbol->isTerminal() == $termOrNot) {
                    $res .= sprintf("\n<br/>%-20s: [", $symbol->getName());
                    for ($i = 0, $len = $symbol->getNbAppearanceIndex(); $i < $len; $i++) {
                        $res .= $symbol->getAppearanceIndex($i) . " ";
                    }
                    $res = substr($res, 0, -1) . "]";
                }
            }
            return $res;
        };

        $res = "Rules";

        for ($i = 0, $len = count($this->productionDefinitions); $i < $len; $i++) {
            $res .= sprintf(" \n<br/>%-3d: %s", $i, $this->productionDefinitions[$i]);
        }

        $res .= "\n<br/>\n<br/>Terminals";
        $res .= $printTerminal(TRUE);
        $res .= "\n<br/>\n<br/>Terminals";
        $res .= $printTerminal(FALSE);


        $res .= "\n<br/>\n<br/>Parsing Method: LALR";

        for ($i = 0; $i < count($this->states); $i++) {
            $res .= "\n<br/>\n<br/>State $i" . $this->states[$i];
        }

        return $res . "\n<br/>";
    }


    public function addProduction(Production $production): int {


        // Get the definition of the production
        $prodDef = $production->getDefinition();

        // Check if the production is already defined
        if (in_array($prodDef, $this->productionDefinitions)) {
            throw new GrammarException("Illegal production: '" . $prodDef . "' Duplicated production");
        }

        if ($production->getName() == "S'") {
            $this->productions[0] = $production;
            $this->productionDefinitions[0] = $prodDef;
            return 1;
        }

        // Save the production definition
        array_push($this->productionDefinitions, $prodDef);

        // Save the production and the return the number of production saved
        return array_push($this->productions, $production);
    }

    public function addSymbol(Symbol $symbol) {

        // Don't add the start Symbol
        if ($symbol->getName() == "S'") {
            return;
        }

        if (!array_key_exists($symbol->getName(), $this->symbols)) {
            // The Symbol is new we just add it to the list
            $this->symbols[$symbol->getName()] = $symbol;
        } else if (!$symbol->isTerminal()) {
            // The symbol exist
            if (!$this->symbols[$symbol->getName()]->isTerminal()) {
                // We have a duplicate rule
                throw new DuplicateRuleGrammarException($symbol->getName());
            }
            // Get back the production appearance from the old symbol
            for ($i = $this->symbols[$symbol->getName()]->getNbAppearanceIndex(); --$i >= 0;) {
                $symbol->addAppearanceIndex($this->symbols[$symbol->getName()]->getAppearanceIndex($i));
            }
            // Replace the symbol by the rule
            $this->symbols[$symbol->getName()] = $symbol;
        }
    }

    private function generateFirstSymbols() {
        $this->firstSymbols = [];


        foreach ($this->symbols as $name => $symbol) {
            $this->firstSymbols[$name] = $symbol->isTerminal() ? [$symbol] : [];
        }

        foreach ($this->getNoTerminal() as $noTerm) {
            for ($i = $noTerm->getNbProduction(); --$i >= 0;) {
                $diff = array_diff($noTerm->getFirstSymbol(), $this->firstSymbols[$noTerm->getName()]);
                $this->firstSymbols[$noTerm->getName()] = array_merge($this->firstSymbols[$noTerm->getName()], $diff);
            }
        }
    }

    /**
     * Compute whatever could be the next terminal for every no terminal symbol.
     */
    private function generateFollowingSymbols() {
        $this->followingSymbols = [];

        foreach ($this->getNoTerminal() as $noTerm) {
            $this->followingSymbols[$noTerm->getName()] = [];
        }

        array_push($this->followingSymbols[$this->getProduction(0)->getSymbol(0)->getName()], '$end');

        do {
            $added = false;
            for ($i = $this->getNbProduction(); --$i >= 1;) {
                $production = $this->getProduction($i);

                for ($j = $production->getNbSymbol(); --$j >= 0;) {
                    $symbol = $production->getSymbol($j);
                    if (!$symbol->isTerminal()) {
                        $nextSymbol = $production->getSymbol($j + 1);
                        if ($nextSymbol != NULL) {
                            $diff = array_diff($this->firstSymbols[$nextSymbol->getName()], $this->followingSymbols[$symbol->getName()]);
                            $this->followingSymbols[$symbol->getName()] = array_merge($this->followingSymbols[$symbol->getName()], $diff);
                            $added = count($diff) == 0 ? false : true;
                        } else {
                            $diff = array_diff($this->followingSymbols[$production->getName()], $this->followingSymbols[$symbol->getName()]);
                            $this->followingSymbols[$symbol->getName()] = array_merge($this->followingSymbols[$symbol->getName()], $diff);
                            $added = count($diff) == 0 ? false : true;
                        }
                    }
                }
            }
        } while ($added);
    }

    private function lr0Closure(array $lrItems) {

        $res = $lrItems;

        for ($i = 0; $i < count($res); $i++) {
            $lrItem = $res[$i];

            foreach ($lrItem->getFollowingProductions() as $followingProd) {
                $firstLrItem = $followingProd->getFirstLrItem();
                if (!in_array($firstLrItem, $res)) {
                    array_push($res, $firstLrItem);
                    $nextLrItems = [];
                    foreach ($firstLrItem->getFollowingProductions() as $followingProdRecursive) {
                        array_push($nextLrItems, $followingProdRecursive->getFirstLrItem());
                    }
                    $diff = array_diff($this->lr0Closure($nextLrItems), $res);
                    $res = array_merge($res, $diff);
                }
            }
        }

        return $res;
    }

    private function lr0Items() {

        $res = $this->lr0Closure([$this->getProduction(0)->getFirstLrItem()]);

        /*
        # Loop over the items in C and each grammar symbols
        i = 0
        while i < len(C):
            I = C[i]
            i += 1

            # Collect all of the symbols that could possibly be in the goto(I,X) sets
            asyms = {}
            for ii in I:
                for s in ii.usyms:
                    asyms[s] = None

            for x in asyms:
                g = self.lr0_goto(I, x)
                if not g or id(g) in self.lr0_cidhash:
                    continue
                    self.lr0_cidhash[id(g)] = len(C)
                C.append(g)
                */


        return $res;
    }

    private function generateTable() {
        $initialLrItems = $this->lr0Items();

        for ($i = 0; $i < count($initialLrItems); $i++) {
            print($initialLrItems[$i]."<br/>\n");
        }

        die();
    }
}
