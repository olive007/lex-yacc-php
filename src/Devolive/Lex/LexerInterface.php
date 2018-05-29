<?php declare(strict_types=1);

namespace Devolive\Lex;

interface LexerInterface {

	function getTokenDefinitionNames() : array;

	function lex($input);

}
