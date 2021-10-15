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

use DateTime;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Model\TokenInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenValidator implements TokenValidatorInterface
{
    protected TokenConfigInterface $config;

    public function __construct(TokenConfigInterface $config)
    {
        $this->config = $config;
    }

    public function validateType(TokenInterface $token, string $type): bool
    {
        $isType = $token->getType() === $type;

        return $this->validate($token) && $isType;
    }

    public function validate(TokenInterface $token): bool
    {
        if (null === $token->getType()) {
            throw new InvalidArgumentException('Missing "Type" in token.');
        }

        $expiresAt = $this->config->getExpiresAt($token);
        $now = new DateTime('now');

        return $expiresAt > $now;
    }
}
