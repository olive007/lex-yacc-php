<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Lex\LexerInterface;
use Devolive\Yacc\Grammar\Grammar;

abstract class AbstractParser {

	// Attributes
	private $lexer;
	private $grammar;
	private $state;


	// Constructor
	public function __construct(LexerInterface $lexer) {

		// Initialize attributes
		$this->lexer = $lexer;

		$this->grammar = new Grammar(new \ReflectionClass(static::class));
		print($this->grammar);

		die();

	}

	public function parse($input) {

		// Get the token list with the lexer
		$tokens = $this->lexer->lex($input);

		return $tokens;

		// Resolve the grammar and return it
		return $this->parse($tokens);

	}

}
