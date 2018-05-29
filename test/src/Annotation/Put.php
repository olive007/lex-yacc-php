<?php declare(strict_types=1);

namespace Annotation;

class Put extends HttpMethod {

	// Constructor
	public function __construct(array $values) {
		parent::__construct($values);
		$this->methodType = "put";
	}

}
