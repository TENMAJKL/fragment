<?php

declare(strict_types=1);

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\CompilerException;
use Majkel\Fragment\Result;

class Chain extends AbstractFunction
{
    public function compile(): Result
    {
        if (count($this->token->children()) < 2) {
            throw new CompilerException('Function o-o excepts at least 2 arguments');
        }

        $last = $this->token->children()[0];

        foreach (array_slice($this->token->children(), 1) as $item) {
            $item->addChild($last);
            $last = $item;
        }
        return $this->parser->parseToken($last);
    }
}
