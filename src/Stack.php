<?php

namespace Majkel\Fragment;

class Stack
{
    private array $data = [];

    public function push(Token $token): static
    {
        $this->data[] = $token;

        return $this;
    }

    public function pop(): Token
    {
        return array_pop($this->data);
    }

    public function top(): Token
    {
        return $this->data[count($this->data) - 1];
    }

    public function empty(): bool
    {
        return 0 === count($this->data);
    }
}
