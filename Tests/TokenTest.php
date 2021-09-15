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
use ReflectionClass;
use SoureCode\Component\Token\Model\Token;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenTest extends TestCase
{
    public function testGetId(): void
    {
        // Arrange
        $id = new Ulid();
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getId());

        $reflectionClass = new ReflectionClass($token);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($token, $id);

        self::assertSame($id->toBase58(), $token->getId()->toBase58());
    }

    public function testGetSetType(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getType());
        $token->setType('foo');
        self::assertSame('foo', $token->getType());
    }

    public function testGetSetData(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getData());
        $token->setData('bar');
        self::assertSame('bar', $token->getData());
    }
}
