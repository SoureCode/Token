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
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenFactory implements TokenFactoryInterface
{
    protected TokenConfigInterface $config;

    /**
     * @var class-string<TokenInterface>
     */
    protected string $tokenClass;

    /**
     * @param class-string<TokenInterface> $tokenClass
     */
    public function __construct(TokenConfigInterface $config, string $tokenClass)
    {
        $this->config = $config;
        $this->tokenClass = $tokenClass;
    }

    public function create(string $type, string $data = null, ?Ulid $id = null): TokenInterface
    {
        if (!$this->config->has($type)) {
            throw new InvalidArgumentException(sprintf('Missing token type "%s"', $type));
        }

        if (null === $id) {
            $id = new Ulid();
        }

        $token = new ($this->tokenClass)($id);
        $token->setType($type);
        $token->setData($data);

        return $token;
    }
}
