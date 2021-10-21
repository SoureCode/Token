<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token\Validator\Constraints;

use Attribute;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute]
class Valid extends Constraint
{
    public ?DateTimeInterface $date = null;

    public ?string $type = null;

    public function __construct(
        mixed $options = null,
        array $groups = null,
        string $type = null,
        DateTimeInterface $date = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->type = $type;
        $this->date = $date;
    }
}
