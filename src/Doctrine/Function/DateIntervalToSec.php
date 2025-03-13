<?php

namespace GrinWay\Service\Doctrine\Function;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * https://www.doctrine-project.org/projects/doctrine-orm/en/current/cookbook/dql-user-defined-functions.html#date-diff
 */
class DateIntervalToSec extends FunctionNode
{
    public $string = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->string = $parser->ArithmeticExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            'CONCAT(IFNULL(REGEXP_SUBSTR(%1$s, "^\-"), ""), "1") *
                (IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[P])([0-9]{1,2}(?=Y))"), 0) * 12 * 4 * 7 * 24 * 60 * 60
                + IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[PY])([0-9]{1,2}(?=M))"), 0) * 28 * 24 * 60 * 60
                + IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[PYM])([0-9]{1,2}(?=D))"), 0) * 24 * 60 * 60
                + IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[T])([0-9]{1,2}(?=H))"), 0) * 60 * 60
                + IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[TH])([0-9]{1,2}(?=M))"), 0) * 60
                + IFNULL(REGEXP_SUBSTR(%1$s, "(?<=[THM])([0-9]{1,2}(?=S))"), 0))
            ',
            $this->string->dispatch($sqlWalker),
        );
    }
}
