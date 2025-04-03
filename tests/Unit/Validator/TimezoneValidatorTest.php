<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\StreetWithoutNumber;
use GrinWay\Service\Validator\StreetWithoutNumberValidator;
use GrinWay\Service\Validator\Timezone;
use GrinWay\Service\Validator\TimezoneValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(Timezone::class)]
#[CoversClass(TimezoneValidator::class)]
class TimezoneValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new TimezoneValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Timezone());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Timezone());
        $this->assertNoViolation();
    }

    public function testValidPhpTimezoneString(): void
    {
        $this->validator->validate('Asia/Tokyo', new Timezone());
        $this->assertNoViolation();
    }

    public function testValidPositiveCarbonTimezoneString(): void
    {
        $this->validator->validate('+12:34', new Timezone());
        $this->assertNoViolation();
    }

    public function testValidOneFrontNumberCarbonTimezoneString(): void
    {
        $this->validator->validate('+1:34', new Timezone());
        $this->assertNoViolation();
    }

    public function testValidOneEndNumberCarbonTimezoneString(): void
    {
        $this->validator->validate('+01:3', new Timezone());
        $this->assertNoViolation();
    }

    public function testValidNegativeCarbonTimezoneString(): void
    {
        $this->validator->validate('-01:00', new Timezone());
        $this->assertNoViolation();
    }

    public function testInvalidPhpTimezoneString(): void
    {
        $this->validator->validate('word/TheOtherWord', new Timezone());

        $this->buildViolation('The {{ timezone }} is not a valid timezone string.')
            ->setParameter('{{ timezone }}', 'word/TheOtherWord')
            ->assertRaised();
    }

    public function testInvalidCarbonSignLessTimezoneString(): void
    {
        $this->validator->validate('01:00', new Timezone());

        $this->buildViolation('The {{ timezone }} is not a valid timezone string.')
            ->setParameter('{{ timezone }}', '01:00')
            ->assertRaised();
    }
}
