<?php declare(strict_types=1);

namespace Devolive\Lex;

class TokenDefinition {


	// Attribute
	private $name;
	private $regex;
	private $action;


	// Constructor
	public function __construct(string $name, string $regex, callable $action) {

		// Initialize attributes
		$this->name = strtoupper($name);
		$this->regex = "/^".$regex."/";
		$this->action = $action;
	}


	// Getter
	public function getName() : string {
		return $this->name;
	}

	public function getRegex() : string {
		return $this->regex;
	}


	// Method
	public function createToken($input) {
		if (preg_match($this->regex, $input, $matches)) {
			return new Token(strlen($matches[0]), $this->name, call_user_func($this->action, $matches));
		}
		return NULL;
	}

	public function __toString() : string {
		return $this->name.": ".$this->regex;
	}

}
