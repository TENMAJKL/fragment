<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Parser;
use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

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

        if ($args[0][0][0] == 'entry') {
            $this->parser->entry();
            $args[0][0][0] = 'main';
        }

        if ($args[3][1] !== Parser::TypesKind[$this->token->children()[2]->content]) {
            throw new CompilerException('Function '.$args[0][0][0].' must return '.$this->token->children()[2]->content);
        }

        return new Result([
            "{$args[2][0][0]} {$args[0][0][0]}({$args[1][0][0]})",
            '{',
            "return {$args[3][0][0]};",
            '}',
        ], TokenKind::Void); 
    }
}
