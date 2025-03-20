<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\AbsolutePath;
use GrinWay\Service\Validator\AbsolutePathValidator;
use GrinWay\Service\Validator\StreetNumber;
use GrinWay\Service\Validator\StreetNumberValidator;
use GrinWay\Service\Validator\StreetWithoutNumber;
use GrinWay\Service\Validator\StreetWithoutNumberValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(StreetNumber::class)]
#[CoversClass(StreetNumberValidator::class)]
class StreetNumberValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new StreetNumberValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new StreetNumber());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new StreetNumber());
        $this->assertNoViolation();
    }

    public function testValidStreetNumber(): void
    {
        $this->validator->validate('11/2,43.42-1_34', new StreetNumber());
        $this->assertNoViolation();
    }

    public function testLetterIsInvalidStreetNumber(): void
    {
        $this->validator->validate('A', new StreetNumber());

        $this->buildViolation('The "{{ street_number }}" is not a street number string.')
            ->setParameter('{{ street_number }}', 'A')
            ->assertRaised();
    }

    public function testSpecialSymbolIsInvalidStreetNumber(): void
    {
        $this->validator->validate('!', new StreetNumber());

        $this->buildViolation('The "{{ street_number }}" is not a street number string.')
            ->setParameter('{{ street_number }}', '!')
            ->assertRaised();
    }

    public function testNonScalarIsInvalidStreetNumber(): void
    {
        $object = new \StdClass();
        $this->validator->validate($object, new StreetNumber());

        $this->buildViolation('The "{{ street_number }}" is not a street number string.')
            ->setParameter('{{ street_number }}', \get_debug_type($object))
            ->assertRaised();
    }
}
