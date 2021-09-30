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
use SoureCode\Component\Token\Exception\RuntimeException;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenConfigTest extends TestCase
{
    protected TokenConfigInterface $config;

    public function testHas(): void
    {
        // Act & Assert
        self::assertTrue($this->config->has('foo'));
        self::assertTrue($this->config->has('bar'));
        self::assertFalse($this->config->has('lorem'));
        self::assertFalse($this->config->has('ipsum'));
    }

    public function testGet(): void
    {
        // Act & Assert
        self::assertArrayHasKey('expiration', $this->config->get('foo'));
        self::assertArrayHasKey('expiration', $this->config->get('bar'));
    }

    public function testGetThrows(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Missing token configuration for type/');

        // Act
        $this->config->get('lorem');
    }

    protected function setUp(): void
    {
        $this->config = new TokenConfig([
            'foo' => ['expiration' => 'PT2H'],
            'bar' => ['expiration' => 'PT4H'],
        ]);
    }
}
