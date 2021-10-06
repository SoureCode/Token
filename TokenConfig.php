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
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Exception\RuntimeException;
use SoureCode\Component\Token\Model\TokenInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenConfig implements TokenConfigInterface
{
    /**
     * @var array<string, array{expiration: DateInterval}>
     */
    protected array $configuration;

    /**
     * @param array<string, array{expiration: string}> $configuration
     *
     * @throws Exception
     */
    public function __construct(array $configuration)
    {
        $this->configuration = array_map(static function (array $config) {
            $config['expiration'] = new DateInterval($config['expiration']);

            return $config;
        }, $configuration);
    }

    /**
     * {@inheritDoc}
     */
    #[ArrayShape(['expiration' => DateInterval::class])]
    public function get(string $type): array
    {
        if (!$this->has($type)) {
            throw new RuntimeException(sprintf('Missing token configuration for type "%s"', $type));
        }

        return $this->configuration[$type];
    }

    public function has(string $type): bool
    {
        return \array_key_exists($type, $this->configuration);
    }

    public function getExpiresAt(TokenInterface $token): DateTimeImmutable
    {
        $config = $this->get($token->getType());
        $interval = $config['expiration'];
        $createdAt = $token->getCreatedAt();

        if (null === $createdAt) {
            throw new InvalidArgumentException('Missing "CreatedAt" timestamp in token.');
        }

        $datetime = $this->cloneDateTime($createdAt);

        return $this->cloneDateTimeImmutable($datetime->add($interval));
    }

    protected function cloneDateTime(DateTimeInterface $datetime): DateTime
    {
        $cloned = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            $datetime->format(DateTimeInterface::ATOM)
        );

        if (!$cloned) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not clone datetime.');
            // @codeCoverageIgnoreEnd
        }

        return $cloned;
    }

    protected function cloneDateTimeImmutable(DateTimeInterface $datetime): DateTimeImmutable
    {
        $cloned = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            $datetime->format(DateTimeInterface::ATOM)
        );

        if (!$cloned) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Could not clone datetime.');
            // @codeCoverageIgnoreEnd
        }

        return $cloned;
    }
}
