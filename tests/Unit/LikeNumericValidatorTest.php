<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Validator\LikeNumeric;
use GrinWay\Service\Validator\LikeNumericValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(LikeNumeric::class)]
#[CoversClass(LikeNumericValidator::class)]
class LikeNumericValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new LikeNumericValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testIntIsValid()
    {
        $this->validator->validate(\PHP_INT_MAX, new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testIntAsStringIsValid()
    {
        $this->validator->validate(\PHP_INT_MAX . '', new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testFloatIsValid()
    {
        $this->validator->validate(\PHP_FLOAT_MAX, new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testFloatAsStringIsValid()
    {
        $this->validator->validate('1.903212380', new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testNegativeFloatAsStringIsValid()
    {
        $this->validator->validate('-1.903212380', new LikeNumeric());
        $this->assertNoViolation();
    }

    public function testCommaStringIsInvalid()
    {
        $this->validator->validate('1,903212380', new LikeNumeric());
        $this->buildViolation('The {{ like_numeric }} is not like numeric.')
            ->setParameter('{{ like_numeric }}', '1,903212380')
            ->assertRaised();
    }

    public function testAlphaStringIsInvalid()
    {
        $this->validator->validate('alpha', new LikeNumeric());
        $this->buildViolation('The {{ like_numeric }} is not like numeric.')
            ->setParameter('{{ like_numeric }}', 'alpha')
            ->assertRaised();
    }
}
