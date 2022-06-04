<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\Result;
use Majkel\Funktor\TokenKind;

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
