<?php

declare(strict_types=1);

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Parser;
use Majkel\Fragment\Result;
use Majkel\Fragment\Token;
use Majkel\Fragment\TokenKind;

class Pair extends AbstractFunction
{
    public function __construct(
        protected Token $token,
        protected Parser $parser
    ) {
        $this->parser->addStructure('Pair', [
            'head' => TokenKind::Any,
            'tail' => 'Pair' 
        ]);
    }

    public function compile(): Result
    {
        $parsed = $this->arguments([
            TokenKind::Any,
            TokenKind::Any
        ]);

        if (!in_array($parsed[1][1], ['Pair', TokenKind::Null])) {
            throw new CompilerException('Argument 2 of function pair must be Pair or null, '.$parsed[1]->type.' given');
        }

        return new Result([
            '{head: '.$parsed[0][0][0].', tail: '.$parsed[1][0][0].'}'
        ], 'Pair');
    }
}
