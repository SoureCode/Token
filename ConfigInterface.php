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
use JetBrains\PhpStorm\ArrayShape;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface ConfigInterface
{
    public function has(string $type): bool;

    #[ArrayShape(['expiration' => DateInterval::class])]
    public function get(string $type): array;
}
