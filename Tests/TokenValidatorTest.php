<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use SoureCode\Component\Token\TokenConfig;
use SoureCode\Component\Token\TokenConfigInterface;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Model\Token;
use SoureCode\Component\Token\TokenValidator;
use SoureCode\Component\Token\TokenValidatorInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenValidatorTest extends TestCase
{
    protected TokenConfigInterface $config;

    protected TokenValidatorInterface $validator;

    public function testValidate(): void
    {
        // Arrange
        $offset = 60 /* seconds */ * 60 /* minutes */ * 3 /* hours */;

        $token = new Token();
        $token->setCreatedAt(new DateTime('@'.time() - $offset));
        $token->setType('bar');

        // Act & Assert
        self::assertTrue($this->validator->validate($token));

        $token->setType('foo');

        self::assertFalse($this->validator->validate($token));
    }

    public function testValidateThrows(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Missing "CreatedAt" timestamp in token/');

        // Arrange
        $token = new Token();
        $token->setType('bar');

        // Act
        $this->validator->validate($token);
    }

    protected function setUp(): void
    {
        $this->config = new TokenConfig([
            'foo' => ['expiration' => 'PT2H'],
            'bar' => ['expiration' => 'PT4H'],
        ]);

        $this->validator = new TokenValidator($this->config);
    }
}
