<?php declare(strict_types=1);

use Devolive\Lex\AbstractLexer;

final class AnnotationLexer extends AbstractLexer {


	// Constructor
	public function __construct() {

		$this->literals = "()[]{},=";

		parent::__construct();
	}

	/** @\w+ */
	public function nameToken($matches) {
		return substr($matches[0], 1);
	}

	/** [a-zA-Z]+\w* */
	public function idToken($matches) {
		return $matches[0];
	}

	/** [0-9]+ */
	public function integerToken($matches) {
		return intval($matches[0]);
	}

	/** [0-9]*\.[0-9]+ */
	public function floatToken($matches) {
		return floatval(matches[0]);
	}

	/** (["'])(.*)\1 */
	public function strToken($matches) {
		return $matches[2];
	}

}
