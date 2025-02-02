<?php

namespace GrinWay\Service\Tests\Unit;

use GrinWay\Service\Validator\AbsolutePath;
use GrinWay\Service\Validator\AbsolutePathValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(AbsolutePath::class)]
#[CoversClass(AbsolutePathValidator::class)]
class AbsolutePathValidatorTest extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new AbsolutePathValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new AbsolutePath());
        $this->assertNoViolation();
    }

    public function testAbsoluteWindowsPathIsValid()
    {
        $this->validator->validate('C:/', new AbsolutePath());
        $this->assertNoViolation();
    }

    public function testAbsoluteUnixPathIsValid()
    {
        $this->validator->validate('/path', new AbsolutePath());
        $this->assertNoViolation();
    }

    public function testRelativePathWithDotIsInvalid()
    {
        $this->validator->validate('./relative/path', new AbsolutePath());

        $this->buildViolation('The path "{{ path }}" is not an absolute one.')
            ->setParameter('{{ path }}', './relative/path')
            ->assertRaised();
    }

    public function testRelativePathWithoutDotIsInvalid()
    {
        $this->validator->validate('relative/path', new AbsolutePath());

        $this->buildViolation('The path "{{ path }}" is not an absolute one.')
            ->setParameter('{{ path }}', 'relative/path')
            ->assertRaised();
    }
}
