<?php declare(strict_types=1);

namespace Devolive\Lex;

use Devolive\Exception\LexerException;

abstract class AbstractLexer implements LexerInterface {

	// Attribute
	private $tokenDefinitions;
	protected $literals;


	// Constructor
	protected function __construct() {

		// Initialise attribute
		$this->tokenDefinitions = [];

		// Reflect on the class. static::class is the children class.
		$itself = new \ReflectionClass(static::class);

		// For each all method of the child class
		foreach ($itself->getMethods() as $method) {

			// Use only the method which its name finish by 'Token'. It is case sensitive !
			if (preg_match("/Token$/", $method->getName())) {

				// Get the name of the token definition
				$name = substr($method->getName(), 0, -5);
				// Get the token regex
				$regex = preg_replace('/[\* \t]+\/$/m', '', preg_replace('/^\/[\* \t]+/m', '', $method->getDocComment()));

				$this->addTokenDefition(new TokenDefinition($name, $regex, function($m) use($method) {
					return $method->invoke($this, $m);
				}));
			}
		}

		foreach (str_split($this->literals) as $literal) {
			$name = $literal;
			$regex = preg_quote($literal);
			$this->addTokenDefition(new TokenDefinition($name, $regex, function($m) {
				return $m[0];
			}));
		}
	}


	// Getter
	public function getTokenDefinitionNames() : array {
		return array_keys($this->tokenDefinitions);
	}


	// Method
	public function addTokenDefition(TokenDefinition $td) {
		if (array_key_exists($td->getName(), $this->tokenDefinitions)) {
			throw new LexerException("Error Processing Request");
		}
		$this->tokenDefinitions[$td->getName()] = $td;
	}

	public function lex($input) {

		$tokens = [];

		$input = preg_replace("/^\s*/", "", $input);
		while (0 < strlen($input)) {
			$token = $this->tokenize($input);
			$input = substr($input, $token->getLength());
			$input = preg_replace("/^\s*/", "", $input);
			array_push($tokens, $token);
		}

		return $tokens;
	}

	private function tokenize($input) {

		foreach ($this->tokenDefinitions as $name => $td) {

			$token = $td->createToken($input);
			if ($token != NULL) {
				return $token;
			}
		}
		throw new LexerException("TokenNotFound");
	}


}
