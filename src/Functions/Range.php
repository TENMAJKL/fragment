<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Parser;
use Majkel\Fragment\Result;
use Majkel\Fragment\Token;
use Majkel\Fragment\TokenKind;

class Range extends AbstractFunction
{
    public function __construct(
        protected Token $token,
        protected Parser $parser
    ) {
        $this->parser->addInternalFunction('range', ['from', 'to'], [
            'return to == from ? null : {head: from, tail: __range(from + 1, to)}'
        ]);
    }

    public function compile(): Result
    {
        $parsed = $this->arguments([
            TokenKind::Int,
            TokenKind::Int
        ]);

       return new Result(['__range('.$parsed[0][0][0].', '.$parsed[1][0][0].')'], 'Pair');
    }
}
