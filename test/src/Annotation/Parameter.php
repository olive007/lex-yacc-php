<?php declare(strict_types=1);

namespace Annotation;

class Parameter {

	// Attribute
	private $name;
	private $regex;
	private $default;

	// Constructor
	function __construct(string $name, string $regex, $default) {
		$this->name = $name;
		$this->regex = $regex;
		$this->default = isset($values['default']) ? $values['default'] : NULL;
	}


	// Getter
	public function getName() {
		return $this->name;
	}

	public function getRegex() {
		return $this->regex;
	}

	public function getDefault() {
		return $this->default;
	}

	public function isRequired() {
		return $this->default === NULL ? true : false;
	}


}
