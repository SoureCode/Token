<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token\Repository;

use SoureCode\Component\Common\Repository\RepositoryInterface;

/**
 * @template T of \SoureCode\Component\Token\Model\TokenInterface
 * @template-implements RepositoryInterface<T>
 */
interface TokenRepositoryInterface extends RepositoryInterface
{
}
