<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Lexer extends TestCase {

	public function lexer() : void {
		$this->assertInstanceOf(
			Email::class,
			Email::fromString('user@example.com')
		);
	}

}
