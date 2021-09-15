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

use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Model\TokenInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Factory implements FactoryInterface
{
    protected ConfigInterface $config;

    /**
     * @var class-string<TokenInterface>
     */
    protected string $tokenClass;

    /**
     * @param class-string<TokenInterface> $tokenClass
     */
    public function __construct(ConfigInterface $config, string $tokenClass)
    {
        $this->config = $config;
        $this->tokenClass = $tokenClass;
    }

    public function create(string $type, string $data = null): TokenInterface
    {
        if (!$this->config->has($type)) {
            throw new InvalidArgumentException(sprintf('Missing token type "%s"', $type));
        }

        $token = new ($this->tokenClass)();
        $token->setType($type);
        $token->setData($data);

        return $token;
    }
}
