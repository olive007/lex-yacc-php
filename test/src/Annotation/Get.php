<?php declare(strict_types=1);

namespace Annotation;

class Get extends HttpMethod {

	// Constructor
	public function __construct(array $values) {
		parent::__construct($values);
		$this->methodType = "get";
	}


}
