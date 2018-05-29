<?php declare(strict_types=1);

namespace Devolive\Yacc;

use Devolive\Lex\LexerInterface;

use Devolive\Exception\ParserException;

abstract class AbstractParser {

	// Attributes
	private $lexer;
	private $grammar;
	private $table;


	// Constructor
	public function __construct(LexerInterface $lexer) {

		// Initialize attributes
		$this->lexer = $lexer;
		$this->grammar = new Grammar(new \ReflectionClass(static::class));
		$this->table = new TableLALR($this->grammar);

		//print($this->grammar);
		print($this->table);

	}

	public function parse($input) {

		$tokens = $this->lexer->lex($input);

		return $tokens;
	}

}
