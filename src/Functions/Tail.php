<?php

namespace Majkel\Fragment\Functions;

use Majkel\Fragment\Result;

class Tail extends AbstractFunction
{
    public function compile(): Result
    {
        $parsed = $this->arguments(['Pair']);
        
        return new Result([$parsed[0][0][0].'["tail"]'], 'Pair');

    }
}
