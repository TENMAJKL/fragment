<?php

declare(strict_types=1);

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class BooleanOperators extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            TokenKind::Int,
            TokenKind::Int,
        ]);

        return new Result([$args[0][0][0].' '.$this->token->content.' '.$args[1][0][0]], TokenKind::Bool);
    }
}
