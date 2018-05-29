<?php declare(strict_types=1);

namespace Annotation;

abstract class HttpMethod {

	// Attribute
	private $path;
	private $parameters;
	protected $methodType;


	// Constructor
	protected function __construct(string $path = "", array $parameters = []) {
		$this->path = $path;
		$this->parameters = $parameters;
	}

}
