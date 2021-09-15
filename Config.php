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
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use SoureCode\Component\Token\Exception\RuntimeException;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Config implements ConfigInterface
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
}
