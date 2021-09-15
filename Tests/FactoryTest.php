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
use SoureCode\Component\Token\Config;
use SoureCode\Component\Token\ConfigInterface;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Factory;
use SoureCode\Component\Token\FactoryInterface;
use SoureCode\Component\Token\Model\Token;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class FactoryTest extends TestCase
{

    protected ConfigInterface $config;

    protected FactoryInterface $factory;

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
        $this->expectExceptionMessageMatches("/Missing token type/");

        // Act
        $token = $this->factory->create('lorem', 'knot');
    }

    protected function setUp(): void
    {
        $this->config = new Config([
            'foo' => ['expiration' => 'PT2H'],
        ]);

        $this->factory = new Factory($this->config, Token::class);
    }

}
