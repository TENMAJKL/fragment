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

        return new Result([
            "(function(world){world.out.push({$args[1][0][0]}); return world;})({$args[0][0][0]})",
        ], 'World');
    } 
}
