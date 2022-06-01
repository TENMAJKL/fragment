<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\Result;
use Majkel\Funktor\TokenKind;

class Definition extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            TokenKind::Variable,
            TokenKind::Variables,
            TokenKind::Type,
            TokenKind::FunctionCall,
        ]);

        return new Result([
            "{$args[2]} {$args[0]}({$args[1]})",
            '{',
            "    return {$args[3]}",
            '}',
        ], TokenKind::Void); 
    }
}
