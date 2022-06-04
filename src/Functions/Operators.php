<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Operators extends AbstractFunction
{
    public function compile(): Result
    {
        $arguments = $this->arguments([
            TokenKind::Int,
            TokenKind::Int
        ]);

        return new Result([
            $arguments[0][0][0].' '.$this->token->content.' '.$arguments[1][0][0]
        ], TokenKind::Int);
    }
}
