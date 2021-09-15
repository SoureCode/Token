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

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface ValidatorInterface
{
    public function validate(TokenInterface $token): bool;
}
