<?php

namespace GrinWay\Service\Tests\Unit\DoctrineUtil;

use GrinWay\Service\Service\DoctrineUtil;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DoctrineUtil::class)]
class DoctrineUtilSetStatementForTest extends AbstractDoctrineUtilTestCase
{
    public function testNotByDefault()
    {
        $setParameterCallback = function ($key, $fieldValue) {
            $this->assertMatchesRegularExpression(
                \sprintf(
                    '~^(?:%s|%s|%s)$~',
                    'TEST_FIELDMODIFIED_TEST_VALUE_1',
                    'TEST_FIELDMODIFIED_TEST_VALUE_2',
                    'TEST_FIELDobject',
                ),
                $key,
            );
            if (\is_object($fieldValue)) {
                $this->assertInstanceOf(\StdClass::class, $fieldValue);
            } else {
                $this->assertMatchesRegularExpression(
                    \sprintf(
                        '~^(?:%s|%s)$~',
                        'TEST_VALUE_1',
                        'TEST_VALUE_2',
                    ),
                    $fieldValue,
                );
            }
        };
        $whereCallback = function ($conditionStatement) {
            $this->assertSame(
                'TEST_ALIAS.TEST_FIELD = :TEST_FIELDMODIFIED_TEST_VALUE_1 OR TEST_ALIAS.TEST_FIELD = :TEST_FIELDMODIFIED_TEST_VALUE_2 OR TEST_ALIAS.TEST_FIELD = :TEST_FIELDobject',
                $conditionStatement,
            );
        };
        $valueToKeyConverterCallback = function (mixed $fieldValue): string {
            if (\is_object($fieldValue)) {
                return 'object';
            }
            return \sprintf('MODIFIED_%s', $fieldValue);
        };

        DoctrineUtil::setStatementFor(
            aliasName: 'TEST_ALIAS',
            fieldName: 'TEST_FIELD',
            statement: 'OR',
            fieldValues: ['TEST_VALUE_1', 'TEST_VALUE_2', new \StdClass()],
            setParameterCallback: $setParameterCallback,
            whereCallback: $whereCallback,
            fieldValueToKeyConverterCallback: $valueToKeyConverterCallback,
        );
    }

    public function testByDefault()
    {
        $stdObject = new \StdClass();
        $stdObjectHash = \spl_object_hash($stdObject);

        $setParameterCallback = function ($key, $fieldValue) use ($stdObjectHash) {
            $this->assertMatchesRegularExpression(
                \sprintf(
                    '~^(?:%s|%s|%s)$~',
                    'TEST_FIELDTEST_VALUE_1',
                    'TEST_FIELDTEST_VALUE_2',
                    \sprintf('TEST_FIELD%s', $stdObjectHash),
                ),
                $key,
            );
            if (\is_object($fieldValue)) {
                $this->assertInstanceOf(\StdClass::class, $fieldValue);
            } else {
                $this->assertMatchesRegularExpression(
                    \sprintf(
                        '~^(?:%s|%s)$~',
                        'TEST_VALUE_1',
                        'TEST_VALUE_2',
                    ),
                    $fieldValue,
                );
            }
        };
        $whereCallback = function ($conditionStatement) use ($stdObjectHash) {
            $this->assertSame(
                \sprintf(
                    'TEST_ALIAS.TEST_FIELD = :TEST_FIELDTEST_VALUE_1 OR TEST_ALIAS.TEST_FIELD = :TEST_FIELDTEST_VALUE_2 OR TEST_ALIAS.TEST_FIELD = :TEST_FIELD%s',
                    $stdObjectHash,
                ),
                $conditionStatement,
            );
        };

        DoctrineUtil::setStatementFor(
            aliasName: 'TEST_ALIAS',
            fieldName: 'TEST_FIELD',
            statement: 'OR',
            fieldValues: ['TEST_VALUE_1', 'TEST_VALUE_2', $stdObject],
            setParameterCallback: $setParameterCallback,
            whereCallback: $whereCallback,
        );
    }
}
