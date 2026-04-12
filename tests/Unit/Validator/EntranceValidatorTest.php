<?php

namespace GrinWay\Service\Tests\Unit\Validator;

use GrinWay\Service\Validator\Entrance;
use GrinWay\Service\Validator\EntranceValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(Entrance::class)]
#[CoversClass(EntranceValidator::class)]
class EntranceValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new EntranceValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Entrance());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Entrance());
        $this->assertNoViolation();
    }

    public function testValidEntrance(): void
    {
        $this->validator->validate('         11  ', new Entrance());
        $this->assertNoViolation();
    }

    public function testLetterIsInvalidEntrance(): void
    {
        $this->validator->validate('A', new Entrance());

        $this->buildViolation('The "{{ entrance }}" is not an entrance string.')
            ->setParameter('{{ entrance }}', 'A')
            ->assertRaised();
    }

    public function testSpecialSymbolIsInvalidEntrance(): void
    {
        $this->validator->validate('!', new Entrance());

        $this->buildViolation('The "{{ entrance }}" is not an entrance string.')
            ->setParameter('{{ entrance }}', '!')
            ->assertRaised();
    }

    public function testNonScalarIsInvalidEntrance(): void
    {
        $object = new \StdClass();
        $this->validator->validate($object, new Entrance());

        $this->buildViolation('The "{{ entrance }}" is not an entrance string.')
            ->setParameter('{{ entrance }}', \get_debug_type($object))
            ->assertRaised();
    }
}
