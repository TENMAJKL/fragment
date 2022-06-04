<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Parser;
use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Args extends AbstractFunction
{
    public function compile(): Result
    {
        if (!$this->parser->in_function) {
            throw new CompilerException('Function args can be callen only in function definition');
        }
        $args = [];
        foreach ($this->token->children() as $child) {
            if ($child->kind !== TokenKind::Variable) {
                throw new CompilerException('Functoon args takes only variables as arguments');
            }
            @[$name, $type] = explode(':', $child->content);
            if (!isset($type)) {
                throw new CompilerException('Variable '.$name.' must have type');
            }
            if (!isset(Parser::TypesKind[$type])) {
                throw new CompilerException('Unknown type: '.$type);
            }
            $this->parser->addVariable($name, Parser::TypesKind[$type]);
            $args[] = $name;
        }        

        return new Result([
            implode(', ', $args),
        ], TokenKind::Arguments);
    }
}
