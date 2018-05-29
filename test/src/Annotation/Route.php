<?php declare(strict_types=1);

namespace Annotation;

class Route {

	// Attribute
	private $prefix;

	// Constructor
	public function __construct(string $prefix = "") {
		$this->prefix = $prefix;
	}

}
