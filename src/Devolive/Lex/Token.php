<?php declare(strict_types=1);

namespace Devolive\Lex;

class Token {

	// Attribute
	private $length;
	private $type;
	private $value;


	// Constructor
	public function __construct(int $length, string $type, $value) {

		// Initialize attributes
		$this->length = $length;
		$this->type = $type;
		$this->value = $value;
	}

	public function getLength() : int {
		return $this->length;
	}

	public function getType() : string {
		return $this->type;
	}

	public function getValue() {
		return $this->value;
	}

	public function __toString() : string {
		return $this->type.": ".$this->value();
	}

}
