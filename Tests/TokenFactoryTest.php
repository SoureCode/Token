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

use PHPUnit\Framework\TestCase;
use SoureCode\Component\Token\TokenConfig;
use SoureCode\Component\Token\TokenConfigInterface;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\TokenFactory;
use SoureCode\Component\Token\TokenFactoryInterface;
use SoureCode\Component\Token\Model\Token;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenFactoryTest extends TestCase
{
    protected TokenConfigInterface $config;

    protected TokenFactoryInterface $factory;

    public function testCreate(): void
    {
        // Act
        $token = $this->factory->create('foo', 'knot');

        // Assert
        self::assertInstanceOf(Token::class, $token);
        self::assertSame('foo', $token->getType());
        self::assertSame('knot', $token->getData());
    }

    public function testCreateThrows(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Missing token type/');

        // Act
        $token = $this->factory->create('lorem', 'knot');
    }

    protected function setUp(): void
    {
        $this->config = new TokenConfig([
            'foo' => ['expiration' => 'PT2H'],
        ]);

        $this->factory = new TokenFactory($this->config, Token::class);
    }
}
