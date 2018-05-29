<?php declare(strict_types=1);

use Devolive\Yacc\AbstractParser;

final class AnnotationParser extends AbstractParser {


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
	 *			   | NAME ( )
	 *			   | NAME ( value )
	 *			   | NAME ( variable )
	 */
	static protected function declarationRule() {
		return [
			function($tokens, $config) {
				$clazz = "Annotation\\".$tokens[0]->getValue();
				return new $clazz();
			},
			function($tokens, $config) {
				$clazz = "Annotation\\".$tokens[0]->getValue();
				return new $clazz();
			},
			function($tokens, $config) {
				$clazz = "Annotation\\".$tokens[0]->getValue();
				return new $clazz($tokens[2]->getValue());
			},
			function($tokens, $config) {
				$clazz = "Annotation\\".$tokens[0]->getValue();
				return new $clazz($tokens[2]->getValue());
			}
		];
	}


	/**
	 * value : INTEGER
	 *		 | FLOAT
	 *		 | STR
	 *		 | object
	 */
	static protected function valueRule() {
		return [
			function($tokens, $config) {
				return $tokens[0]->getValue();
			},
			function($tokens, $config) {
				return $tokens[0]->getValue();
			},
			function($tokens, $config) {
				return $tokens[0]->getValue();
			},
			function($tokens, $config) {
				return $tokens[0]->getValue();
			}
		];
	}


	/**
	 * variable : ID = value
	 */
	static protected function variableRule() {
		return [
			function($tokens, $config) {
				return $tokens[2]->getValue();
			}
		];
	}


	/**
	 * object_list : ID : value
	 *             | object_list , ID : value
	 */
	static protected function objectListRule() {
		return [
			function($tokens, $config) {
				return [$tokens[1]->getValue() => $tokens[2]->getValue];
			},
			function($tokens, $config) {
				return [$tokens[3]->getValue() => $tokens[4]->getValue];
			}
		];
	}



	/**
	 * object : { }
	 *        | { object_list }
	 */
	static protected function objectRule() {
		return [
			function($tokens, $config) {
				return [];
			},
			function($tokens, $config) {
				return $tokens[2];
			}
		];
	}

}
