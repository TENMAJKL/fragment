<?php

declare(strict_types=1);

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Output extends AbstractFunction
{
    public function compile(): Result
    {
        $args = $this->arguments([
            TokenKind::Any,
            'World',
        ]);

        $this->parser->addInternalFunction('echo', ['target', 'world'], ['world.out.push(target)', 'return world']);

        return new Result([
            "__echo({$args[0][0][0]}, {$args[1][0][0]})",
        ], 'World');
    }
}
