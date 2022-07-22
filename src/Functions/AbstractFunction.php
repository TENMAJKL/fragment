<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Parser;
use Majkel\Fragment\Result;
use Majkel\Fragment\Token;
use Majkel\Fragment\TokenKind;

abstract class AbstractFunction
{
    public function __construct(
        protected Token $token,
        protected Parser $parser
    ) {
    }

    abstract public function compile(): Result;

    protected function arguments(array $signature): array
    {
        $result = [];
        if ($s = count($signature) !== $t = count($this->token->children())) {
            throw new CompilerException('Function '.$this->token->content.' takes exactly '.$s.' arguments but '.$t.' given');
        }
        foreach ($signature as $index => $type) {
            $token = $this->token->children()[$index];
            $parsed = $this->parser->parseToken($token);
            if (TokenKind::FunctionCall !== $type && TokenKind::Any !== $type) {
                if ($parsed->type !== $type) {
                    throw new CompilerException('Argument '.($index + 1).' of function '.$this->token->content.' must be TODO at line '.$token->line);
                }
            }

            $result[] = [$parsed->content, $parsed->type];
        }

        return $result;
    }
}
