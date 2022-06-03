<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\Parser;
use Majkel\Funktor\Result;
use Majkel\Funktor\Token;

class UserFunction extends AbstractFunction
{
    public function getParameters(Token $function): array
    {
        $params = [];

        foreach ($function->children()[1]->children() as $param) {
            $params[] = Parser::TypesKind[explode(':', $param->content)[1]];
        }
        return $params;
    }

    public function compile(): Result
    {
        $function = $this->parser->getFunction($this->token->content);
        $args = $this->arguments(
            $this->getParameters($function)
        );

        $args = array_reduce(array_slice($args, 1), fn($carry, $item) => $carry.', '.$item[0][0], $args[0][0]);
        return new Result([
            "{$this->token->content}({$args})"
        ], Parser::TypesKind[$function->children()[2]->content]);
    }
}
