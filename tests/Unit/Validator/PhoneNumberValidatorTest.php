<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\PhoneNumber;
use GrinWay\Service\Validator\PhoneNumberValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(PhoneNumber::class)]
#[CoversClass(PhoneNumberValidator::class)]
class PhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new PhoneNumberValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testValidPhpPhoneNumberString(): void
    {
        $this->validator->validate('+0 000 000 00 00', new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testValidPositiveCarbonPhoneNumberString(): void
    {
        $this->validator->validate('0000000000', new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testValidOneFrontNumberCarbonPhoneNumberString(): void
    {
        $this->validator->validate('0 (000) 000 00 00', new PhoneNumber());
        $this->assertNoViolation();
    }

    public function testInvalidPhpPhoneNumberString(): void
    {
        $this->validator->validate('0 000 000 00 .0', new PhoneNumber());

        $this->buildViolation('The "{{ phone_number }}" is not a valid phone number string.')
            ->setParameter('{{ phone_number }}', '0 000 000 00 .0')
            ->assertRaised();
    }
}
