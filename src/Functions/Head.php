<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Head extends AbstractFunction
{
    public function compile(): Result
    {
        $parsed = $this->arguments([
            'Pair',
        ]);

        return new Result([$parsed[0][0][0].'["head"]'], TokenKind::Any);
    }
}
