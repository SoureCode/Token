<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token;

use DateInterval;
use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use SoureCode\Component\Token\Model\TokenInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface TokenConfigInterface
{
    /**
     * @param string $type
     *
     * @return array{expiration: DateInterval}
     */
    #[ArrayShape(['expiration' => DateInterval::class])]
    public function get(string $type): array;

    public function getExpiresAt(TokenInterface $token): DateTimeImmutable;

    public function has(string $type): bool;

}
