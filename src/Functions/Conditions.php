<?php

declare(strict_types=1);

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Conditions extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            TokenKind::Bool,
            TokenKind::Any,
            TokenKind::Any,
        ]);

        if ($args[1][1] !== $args[2][1]) {
            throw new CompilerException('Argument 1 must return same value as argument 2 of function if');
        }

        return new Result([
            "({$args[0][0][0]}) ? ({$args[1][0][0]}) : ({$args[2][0][0]})",
        ], $args[1][1]);
    }
}
