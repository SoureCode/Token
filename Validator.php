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
use DateTime;
use DateTimeInterface;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Exception\RuntimeException;
use SoureCode\Component\Token\Model\TokenInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Validator implements ValidatorInterface
{

    protected ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function validate(TokenInterface $token): bool
    {
        $type = $token->getType();
        $config = $this->config->get($type);

        $expireAt = $this->getExpiresAt($token, $config['expiration']);
        $now = new DateTime('now');

        return $expireAt > $now;
    }

    protected function getExpiresAt(TokenInterface $token, DateInterval $interval): DateTime
    {
        $createdAt = $token->getCreatedAt();

        if (null === $createdAt) {
            throw new InvalidArgumentException('Missing "CreatedAt" timestamp in token.');
        }

        $datetime = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            $createdAt->format(DateTimeInterface::ATOM)
        );

        if (!$datetime) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not clone datetime.');
            // @codeCoverageIgnoreEnd
        }

        return $datetime->add($interval);
    }
}
