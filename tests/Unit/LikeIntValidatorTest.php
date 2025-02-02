<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Validator\LikeInt;
use GrinWay\Service\Validator\LikeIntValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(LikeInt::class)]
#[CoversClass(LikeIntValidator::class)]
class LikeIntValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new LikeIntValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new LikeInt());
        $this->assertNoViolation();
    }

    public function testIntIsValid()
    {
        $this->validator->validate(1, new LikeInt());
        $this->assertNoViolation();
    }

    public function testStringIntIsValid()
    {
        $this->validator->validate('001', new LikeInt());
        $this->assertNoViolation();
    }

    public function testStringFloatIsInvalid()
    {
        $this->validator->validate('1.1', new LikeInt());

        $this->buildViolation('It is not like int: {{ like_int }}.')
            ->setParameter('{{ like_int }}', '1.1')
            ->assertRaised();
    }

    public function testFloatIsInvalid()
    {
        $this->validator->validate(1.2, new LikeInt());

        $this->buildViolation('It is not like int: {{ like_int }}.')
            ->setParameter('{{ like_int }}', '1.2')
            ->assertRaised();
    }

    public function testFloatThatConvertsToTheStringExactlyLikeIntIsValid()
    {
        $this->validator->validate(1., new LikeInt());
        $this->assertNoViolation();
    }
}
