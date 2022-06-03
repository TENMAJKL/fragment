<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\Result;
use Majkel\Funktor\TokenKind;

class Definition extends AbstractFunction
{
    public function compile(): Result
    {
        $this->parser->addFunction($this->token); // Allows recursion
 
        $args = $this->arguments([
            TokenKind::FunctionName,
            TokenKind::Variables,
            TokenKind::Type,
            TokenKind::FunctionCall,
        ]);

        $this->parser->removeVariables($this->token->children()[1]);

        if ($args[0][0] == 'entry') {
            $this->parser->entry();
            $args[0][0] = 'main';
        }

        return new Result([
            "{$args[2][0]} {$args[0][0]}({$args[1][0]})",
            '{',
            "return {$args[3][0]};",
            '}',
        ], TokenKind::Void); 
    }
}
