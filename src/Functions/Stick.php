<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class Stick extends AbstractFunction
{
    public function compile(): Result
    {
        $parsed = $this->arguments([
            TokenKind::String,
            TokenKind::String,
        ]);
        return new Result([$parsed[0][0][0].' + '.$parsed[0][0][0]], TokenKind::String);
    }
}
