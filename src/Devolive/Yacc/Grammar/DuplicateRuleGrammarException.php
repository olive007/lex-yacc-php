<?php


namespace Devolive\Yacc\Grammar;


class DuplicateRuleGrammarException extends \Exception {

    /**
     * @param string $symbolName  Name of the duplicated symbol
     */
    public function __construct(string $symbolName) {
        parent::__construct("Illegal rule: '".$symbolName()."' Duplicated rule");
    }
}