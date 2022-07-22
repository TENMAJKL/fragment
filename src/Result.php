<?php

declare(strict_types=1);

namespace Majkel\Fragment;

class Result
{
    public function __construct(
        public readonly array $content,
        public readonly TokenKind|string $type,
    ) {
    }

    public function __toString()
    {
        return $this->content[0];
    }
}
