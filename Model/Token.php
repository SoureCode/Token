<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token\Model;

use SoureCode\Component\Common\Model\CreatedAtTrait;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Token implements TokenInterface
{
    use CreatedAtTrait;

    protected ?Ulid $id = null;

    protected ?string $type = null;

    protected ?string $data = null;

    public function __construct(Ulid $id)
    {
        $this->id = $id;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): void
    {
        $this->data = $data;
    }
}
