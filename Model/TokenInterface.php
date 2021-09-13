<?php

namespace SoureCode\Component\Token\Model;

use SoureCode\Component\Common\Model\CreatedAtInterface;
use Symfony\Component\Uid\Ulid;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
interface TokenInterface extends CreatedAtInterface
{
    public function getId(): ?Ulid;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getData(): ?string;

    public function setData(?string $data): void;
}
