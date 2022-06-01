<?php

namespace Majkel\Funktor\Functions;

use Majkel\Funktor\CompilerException;
use Majkel\Funktor\Parser;
use Majkel\Funktor\Result;
use Majkel\Funktor\Token;
use Majkel\Funktor\TokenKind;

abstract class AbstractFunction
{
    public function __construct(
        protected Token $token,
        protected Parser $parser
    ) {
        
    }

    protected function arguments(array $signature): array
    {
        $result = [];
        foreach ($signature as $index => $type) {
            $token = $this->token->children()[$index];
            $parsed = $this->parser->parseToken($token);
            if ($type !== TokenKind::FunctionCall) {
                if ($parsed->type !== $type) {
                    throw new CompilerException('Argument '.($index + 1).' of function '.$this->token->content.' must be TODO at line '.$token->line);
                }
            }

            $result[] = $parsed;
        }

        return $result;
    }

    abstract public function compile(): Result;
}
