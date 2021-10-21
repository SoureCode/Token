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

use DateTime;
use SoureCode\Component\Token\Exception\InvalidArgumentException;
use SoureCode\Component\Token\Model\TokenInterface;
use SoureCode\Component\Token\Repository\TokenRepositoryInterface;
use SoureCode\Component\Token\TokenConfigInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class ValidValidator extends ConstraintValidator
{
    protected TokenConfigInterface $config;

    protected TokenRepositoryInterface $tokenRepository;

    public function __construct(TokenConfigInterface $config, TokenRepositoryInterface $tokenRepository)
    {
        $this->config = $config;
        $this->tokenRepository = $tokenRepository;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Valid) {
            throw new UnexpectedTypeException($constraint, Valid::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $token = $this->getToken($value);

        if (null === $token) {
            $this->context->buildViolation('Token "{{ id }}" not found.')
                ->setParameter('{{ id }}', (string) $value)
                ->addViolation();

            return;
        }

        $type = $token->getType();

        if (null === $type) {
            throw new InvalidArgumentException('Missing type in token.');
        }

        if (!$this->config->has($type)) {
            $this->context->buildViolation('Missing token configuration for token type "{{ type }}".')
                ->setParameter('{{ type }}', $type)
                ->addViolation();

            return;
        }

        if (null !== $constraint->type && $type !== $constraint->type) {
            $this->context->buildViolation('Mismatching token type. Expected "{{ expected }}", given: "{{ given }}".')
                ->setParameter('{{ expected }}', $constraint->type)
                ->setParameter('{{ given }}', $type)
                ->addViolation();
        }

        $expiresAt = $this->config->getExpiresAt($token);
        $date = $constraint->date ?? new DateTime('now');

        if ($expiresAt <= $date) {
            $this->context->buildViolation('Token "{{ id }}" expired.')
                ->setParameter('{{ id }}', (string) $token->getId())
                ->addViolation();
        }
    }

    private function getToken(mixed $value): ?TokenInterface
    {
        if ($value instanceof TokenInterface) {
            return $value;
        }

        if (!is_a($value, Ulid::class)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, Ulid::class);
        }

        return $this->tokenRepository->find($value);
    }
}
