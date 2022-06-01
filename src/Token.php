<?php

namespace Majkel\Funktor;

class Token
{
    public function __construct(
        public readonly TokenKind $kind,
        public readonly string $content,
        private array $children,
        private int $line,
    ) {
        
    }

    public function addChild(Token $token): static
    {
        $this->children[] = $token;
        return $this;
    }

    public function children(): array
    {
        return $this->children;
    }
}
