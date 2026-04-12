<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\Street;
use GrinWay\Service\Validator\StreetValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(Street::class)]
#[CoversClass(StreetValidator::class)]
class StreetValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new StreetValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Street());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Street());
        $this->assertNoViolation();
    }

    public function testAsciiValidStreetString(): void
    {
        $this->validator->validate('3th (Ascii),_street', new Street());
        $this->assertNoViolation();
    }

    public function testUnicodeValidStreetString(): void
    {
        $this->validator->validate('Хошмар - / \\ на улице йцукенгшщзхъфывапролджэячсмитьбю.', new Street());
        $this->assertNoViolation();
    }

    public function testStreetWithIntIsIncorrect(): void
    {
        $this->validator->validate('Street qwertyuiopasdfghjklzxcvbnm 1', new Street());

        $this->assertNoViolation();
    }

    public function testNonScalarIsInvalidStreetWithoutNumber(): void
    {
        $object = new \StdClass();
        $this->validator->validate($object, new Street());

        $this->buildViolation('The "{{ street }}" is not a street string.')
            ->setParameter('{{ street }}', \get_debug_type($object))
            ->assertRaised();
    }
}
