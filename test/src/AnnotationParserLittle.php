<?php declare(strict_types=1);

use Devolive\Yacc\AbstractParser;

final class AnnotationParserLittle extends AbstractParser {


	// Grammar
	/**
	 * declarations : declaration
	 *				| declarations declaration
	 */
	static protected function firstRule() { // Declarations Rule
		return [
			function($tokens, $config) {
				return [$tokens[0]->getValue()];
			},
			function($tokens, $config) {
				return array_merge($tokens[0]->getValue(), [$tokens[1]->getValue()]);
			}
		];
	}

	/**
	 * declaration : NAME
	 */
	static protected function declarationRule() {
		return [
			function($tokens, $config) {
				$clazz = "Annotation\\".$tokens[0]->getValue();
				return new $clazz();
			}
		];
	}
}
