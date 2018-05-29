<?php declare(strict_types=1);

namespace Swad\Parser;

use Swad\Exception\ParserException;

class LrItem {


	// Attribute
	private $rule;
	private $readingIndex;


	// Constructor
	function __construct(Rule $rule, int $readingIndex) {

		$this->rule = $rule;
		$this->readingIndex = $readingIndex;

	}


	// Getter
	public function getRule() : Rule {
		return $this->rule;
	}


	// Method
	public function __toString(): string {

		$res = $this->rule->getName()." -> ";

		for ($i = 0; $i < $this->rule->getNbrComponent(); $i++) {

			if ($i == $this->readingIndex) {
				$res .= ". ";
			}
			$res .= $this->rule->getComponent($i)." ";

		}
		if ($i == $this->readingIndex) {
			$res .= ". ";
		}


		return substr($res, 0, -1);
	}


}
