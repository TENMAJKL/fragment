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

        if ($this->parser->in_function) {
            throw new CompilerException('Function can\'t be defined inside other function definition');
        }
        $this->parser->in_function = true;

        $args = $this->arguments([
            TokenKind::FunctionName,
            TokenKind::Arguments,
            TokenKind::Type,
            TokenKind::FunctionCall,
        ]);

        $this->parser->in_function = false;

        $this->parser->removeVariables($this->token->children()[1]);

        if ($args[3][1] !== $this->parser->getType($this->token->children()[2]->content)) {
            throw new CompilerException('Function '.$args[0][0][0].' must return '.$this->token->children()[2]->content);
        }

        $fn = [
            "function {$args[0][0][0]}({$args[1][0][0]}) {",
            "return {$args[3][0][0]}",
            '}',
        ];

        if ($args[0][0][0] == 'entry') {
            $this->parser->entry();
            $fn = [
                'function end(world) {',
                'world.out.forEach((item) => console.log(item))',
                '}',
                'var _world = {',
                '"out": []',
                '}',
                "end((function({$args[1][0][0]}) {",
                "return {$args[3][0][0]}",
                '})(_world))',
            ];
        }

        return new Result($fn, TokenKind::Void); 
    }
}
