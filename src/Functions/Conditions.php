<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\CompilerException;
use Majkel\Funktor\Result;
use Majkel\Funktor\TokenKind;

class Conditions extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            TokenKind::Bool,
            TokenKind::Any,
            TokenKind::Any
        ]);

        if ($args[1][1] !== $args[2][1]) {
            throw new CompilerException('Argument 1 must return same value as argument 2 of function if');
        }

        return new Result([
            "({$args[0][0][0]}) ? ({$args[1][0][0]}) : ({$args[2][0][0]})"
        ], $args[1][1]);
    }
}
