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

use SoureCode\Component\Token\Model\TokenInterface;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface TokenFactoryInterface
{
    public function create(Ulid $id, string $type, string $data = null): TokenInterface;
}
