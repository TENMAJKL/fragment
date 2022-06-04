<?php

namespace Majkel\Fragment;

class Result
{
    public function __construct(
        public readonly array $content,
        public readonly TokenKind $type,
    ) {
        
    }

    public function __toString()
    {
        return $this->content[0];
    }
}
