<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\StreetWithoutNumber;
use GrinWay\Service\Validator\StreetWithoutNumberValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(StreetWithoutNumber::class)]
#[CoversClass(StreetWithoutNumberValidator::class)]
class StreetValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new StreetWithoutNumberValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new StreetWithoutNumber());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new StreetWithoutNumber());
        $this->assertNoViolation();
    }

    public function testAsciiValidStreetString(): void
    {
        $this->validator->validate('(Ascii),_street', new StreetWithoutNumber());
        $this->assertNoViolation();
    }

    public function testUnicodeValidStreetString(): void
    {
        $this->validator->validate('Хошмар - / \\ на улице вязов.', new StreetWithoutNumber());
        $this->assertNoViolation();
    }

    public function testStreetWithIntIsIncorrect(): void
    {
        $this->validator->validate('Street 1', new StreetWithoutNumber());

        $this->buildViolation('The "{{ street }}" is not a street string.')
            ->setParameter('{{ street }}', 'Street 1')
            ->assertRaised();
    }

    public function testNonScalarIsInvalidStreetWithoutNumber(): void
    {
        $object = new \StdClass();
        $this->validator->validate($object, new StreetWithoutNumber());

        $this->buildViolation('The "{{ street }}" is not a street string.')
            ->setParameter('{{ street }}', \get_debug_type($object))
            ->assertRaised();
    }
}
