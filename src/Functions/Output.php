<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Output extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            'World',
            TokenKind::Any,
        ]);

        $this->parser->addInternalFunction('echo', ['world', 'target'], ['world.out.push(target)', 'return world']);

        return new Result([
            "__echo({$args[0][0][0]}, {$args[1][0][0]})",
        ], 'World');
    }
}
