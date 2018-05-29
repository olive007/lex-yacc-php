<?php declare(strict_types=1);

//ini_set('display_errors', 0);

//opcache_reset();

require_once __DIR__.'/../../vendor/autoload.php';

$parser = new AnnotationParser(new AnnotationLexer());

print_r($parser->parse("@Get('test')"));
