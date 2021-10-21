<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Component\Token\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use SoureCode\Component\Token\Model\Token;
use SoureCode\Component\Token\Repository\TokenRepositoryInterface;
use SoureCode\Component\Token\TokenConfig;
use SoureCode\Component\Token\TokenConfigInterface;
use SoureCode\Component\Token\Validator\Constraints\Valid;
use SoureCode\Component\Token\Validator\Constraints\ValidValidator;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class TokenValidatorTest extends TestCase
{
    protected ?TokenConfigInterface $config = null;

    protected ?TokenRepositoryInterface $repository = null;

    protected ?ValidatorInterface $validator = null;

    public function testExpired(): void
    {
        // Arrange
        $token = new Token($tokenId = new Ulid());
        $token->setType('foo');
        $token->setCreatedAt(new DateTime('-2 hours'));
        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid()]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame(sprintf('Token "%s" expired.', (string) $tokenId), $violation->getMessage());
    }

    public function testExpiredCustomDate(): void
    {
        // Arrange
        $token = new Token($tokenId = new Ulid());
        $token->setType('foo');
        $token->setCreatedAt(new DateTime('-1 hour'));
        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid(date: new DateTime('+1 hour'))]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame(sprintf('Token "%s" expired.', (string) $tokenId), $violation->getMessage());
    }

    public function testInvalidType(): void
    {
        // Act
        $violations = $this->validator->validate('test', [new Valid()]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame(sprintf('This value should be of type %s.', Ulid::class), $violation->getMessage());
    }

    public function testMismatchingType(): void
    {
        // Arrange
        $token = new Token($tokenId = new Ulid());
        $token->setType('foo');
        $token->setCreatedAt(new DateTime());
        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid(type: 'bar')]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame('Mismatching token type. Expected "bar", given: "foo".', $violation->getMessage());
    }

    public function testMissingConfiguration(): void
    {
        // Arrange
        $token = new Token($tokenId = new Ulid());
        $token->setType('lorem');
        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid()]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame('Missing token configuration for token type "lorem".', $violation->getMessage());
    }

    public function testMissingToken(): void
    {
        // Arrange
        $this->repository->method('find')
            ->willReturn(null);

        // Act
        $violations = $this->validator->validate($tokenId = new Ulid(), [new Valid()]);

        // Assert
        self::assertCount(1, $violations);
        /**
         * @var ConstraintViolationInterface $violation
         */
        $violation = $violations[0];

        self::assertSame(sprintf('Token "%s" not found.', (string) $tokenId), $violation->getMessage());
    }

    public function testValidCustomDate(): void
    {
        // Arrange
        $token = new Token($tokenId = new Ulid());
        $token->setType('foo');
        $token->setCreatedAt(new DateTime('+1 hour'));
        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid(date: new DateTime('-1 hour'))]);

        // Assert
        self::assertCount(0, $violations);
    }

    public function testValidToken(): void
    {
        // Arrange
        $offset = 60 /* seconds */ * 60 /* minutes */ * 3 /* hours */
        ;

        $token = new Token($tokenId = new Ulid());
        $token->setCreatedAt(new DateTime('@'.time() - $offset));
        $token->setType('bar');

        $this->repository->method('find')
            ->willReturn($token);

        // Act
        $violations = $this->validator->validate($token, [new Valid()]);

        // Assert
        self::assertCount(0, $violations);
    }

    protected function setUp(): void
    {
        $this->config = new TokenConfig([
            'foo' => ['expiration' => 'PT2H'],
            'bar' => ['expiration' => 'PT4H'],
        ]);

        $this->repository = $this->createMock(TokenRepositoryInterface::class);

        $constraintValidator = new ValidValidator($this->config, $this->repository);

        $factory = new class($constraintValidator) implements ConstraintValidatorFactoryInterface {
            private ConstraintValidatorFactoryInterface $fallback;

            private ValidValidator $validValidator;

            public function __construct(ValidValidator $validValidator)
            {
                $this->fallback = new ConstraintValidatorFactory();
                $this->validValidator = $validValidator;
            }

            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                if ($constraint instanceof Valid) {
                    return $this->validValidator;
                }

                return $this->fallback->getInstance($constraint);
            }
        };

        $builder = Validation::createValidatorBuilder();

        $builder->setConstraintValidatorFactory($factory);

        $this->validator = $builder->getValidator();
    }

    protected function tearDown(): void
    {
        $this->validator = null;
        $this->config = null;
        $this->repository = null;
    }
}
