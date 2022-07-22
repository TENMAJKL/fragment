<?php

declare(strict_types=1);

namespace Majkel\Fragment;

class Codegen
{
    public function __construct(
        private array $template,
        private array $functions = [],
    ) {
    }

    public function generate(): string
    {
        $indent = '';
        $result = '';

        foreach ($this->functions as $name => $function) {
            $result .= 'function __'.$name.'('.implode(', ', $function[0]).") {\n    ".implode("\n    ", $function[1])."\n}\n";
        }

        foreach ($this->template as $item) {
            foreach ($item->content as $line) {
                if (str_starts_with($line, '}')) {
                    $indent = substr($indent, 1, -4);
                }
                $result .= $indent.$line;
                if (str_ends_with($line, '{')) {
                    $indent .= '    ';
                }
                $result .= "\n";
            }
        }

        return $result;
    }
}
