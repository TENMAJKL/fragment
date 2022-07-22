<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;
use Majkel\Fragment\TokenKind;

class IsNull extends AbstractFunction
{
    public function compile(): Result
    {
        $result = $this->arguments([TokenKind::Any]);

        return new Result([$result[0][0][0].'=== null'], TokenKind::Bool);
    }
}
