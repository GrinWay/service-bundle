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
        ?callable $fieldValueToKeyConverterCallback = null,
        string    $operator = '=',
    ): void
    {
        if (empty($fieldValues)) {
            return;
        }

        Validation::createCallable(new NotBlank())($statement);
        Validation::createCallable(new NotBlank())($aliasName);
        Validation::createCallable(new NotBlank())($fieldName);

        $fieldValueToKeyConverterCallback ??= static function (mixed $fieldValue): string {
            if (\is_object($fieldValue)) {
                return \spl_object_hash($fieldValue);
            }
            return (string)$fieldValue;
        };

        $statement = \strtoupper($statement);
        $startConditionStatement = \sprintf(
            ' %s ',
            $statement,
        );

        $conditionStatement = '';
        foreach ($fieldValues as $fieldValue) {
            $key = \sprintf('%s%s', $fieldName, $fieldValueToKeyConverterCallback($fieldValue));
            $expKey = \sprintf(':%s', $key);
            $conditionStatement .= \sprintf(
                '%s%s.%s %s %s',
                $startConditionStatement,
                $aliasName,
                $fieldName,
                $operator,
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
