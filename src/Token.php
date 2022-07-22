<?php

namespace Majkel\Fragment;

class Token
{
    public function __construct(
        public readonly TokenKind $kind,
        public string $content,
        private array $children,
        public readonly int $line,
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
