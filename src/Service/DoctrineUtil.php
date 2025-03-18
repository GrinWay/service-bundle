<?php

namespace GrinWay\Service\Service;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class DoctrineUtil
{
    public static function setStatementFor(
        string    $aliasName,
        string    $fieldName,
        string    $statement,
        array     $fieldValues,
        callable  $setParameterCallback,
        callable  $whereCallback,
        ?callable $valueToKeyConverter = null,
    ): void
    {
        if (empty($fieldValues)) {
            return;
        }

        Validation::createCallable(new NotBlank())($statement);
        Validation::createCallable(new NotBlank())($aliasName);
        Validation::createCallable(new NotBlank())($fieldName);

        $valueToKeyConverter ??= static function (mixed $value): string {
            if (\is_object($value)) {
                return \spl_object_hash($value);
            }
            return (string)$value;
        };

        $statement = \strtoupper($statement);
        $startConditionStatement = \sprintf(
            ' %s ',
            $statement,
        );

        $conditionStatement = '';
        foreach ($fieldValues as $fieldValue) {
            $key = \sprintf('%s%s', $fieldName, $valueToKeyConverter($fieldValue));
            $expKey = \sprintf(':%s', $key);
            $conditionStatement .= \sprintf(
                '%s%s.%s = %s',
                $startConditionStatement,
                $aliasName,
                $fieldName,
                $expKey,
            );
            $setParameterCallback($key, $fieldValue);
        }
        $conditionStatement = \substr($conditionStatement, \strlen($startConditionStatement));
        if (!empty($conditionStatement)) {
            $whereCallback($conditionStatement);
        }
    }
}
