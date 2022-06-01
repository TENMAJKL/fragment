<?php

namespace Majkel\Funktor;

class Codegen
{
    public function __construct(
        private array $template,
        private array $libs = [],
        private array $functions = [],
        private array $structures = [],
    ) {
        
    }
    
    public function generate()
    {

    }

}
