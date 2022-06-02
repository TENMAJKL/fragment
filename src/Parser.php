<?php

namespace Majkel\Funktor;

use Majkel\Funktor\Functions\Definition;
use Majkel\Funktor\Functions\Operators;

class Parser
{
    private array $libs = [];

    private array $functions = [];

    private array $structures = [];

    private bool $entry = false;

    public const Types = [
        'string' => 'char*', // TODO
        'int' => 'int',
    ];

    public function __construct(
        private array $tokens
    ) {
        
    }

    public function parse(): array
    {
        $result = array_map([$this, 'parseToken'], $this->tokens);

        if (!$this->entry) {
            throw new CompilerException('Entry function is missing');
        }

        return $result;
    }

    public function parseToken(Token $token): Result
    {
        return match ($token->kind) {
            TokenKind::Int => new Result([$token->content], TokenKind::Int),
            TokenKind::String => new Result(['"'.substr($token->content, 1, -1).'"'], TokenKind::String),
            TokenKind::FunctionCall => $this->parseFunctionCall($token),
            TokenKind::FunctionDefinition => $this->parseFunctionDefinition($token),
            TokenKind::Variables => $this->parseVariables($token),
            TokenKind::Type => $this->parseType($token),
            TokenKind::Variable => new Result([$token->content], TokenKind::Variable)
        };
    }

    public function parseType(Token $token): Result
    {
        if (!isset(self::Types[$token->content])) {
            throw new CompilerException('Type '.$token->content.' does not exist');
        }

        return new Result([self::Types[$token->content]], TokenKind::Type);
    }

    public function parseVariables(Token $token): Result
    {
        $result = [];
        foreach ($token->children() as $var) {
            @[$name, $type] = explode(':', $var->content);
            if (!isset($var)) {
                throw new CompilerException('Variable '.$name.' must have type');
            }
            $result[] = $type.' '.$name;
        }
        return new Result([implode(', ', $result)], TokenKind::Variables);
    }

    public function parseFunctionDefinition(Token $token): Result
    {
        return (new Definition($token, $this))->compile();  
    }

    public function parseFunctionCall(Token $token): Result
    {
        return (new (match($token->content) {
            '+','-','/','*' => Operators::class,
            default => $this->parseUserFunction($token),
        })($token, $this))->compile();
    }

    public function parseUserFunction(Token $token): Result
    {
        if (!isset($this->functions[$token->content])) {
            throw new CompilerException('Function '.$token->content.' does not exist');
        }

        return new Result([$token->content.'('.$this->parse($token->children()).')'], $this->functions[$token->content]->children()[2]);
    }

    public function addLib(string $lib): static
    {
        $this->libs[] = $lib;
        return $this;
    }

    public function addFunction(Token $function): static
    {
        $name = $function->children()[0]->content;
        if (isset($this->functions[$name])) {
            throw new CompilerException('Function '.$name.' has already been defined');
        }
        $this->functions[$name] = $function;
        return $this;
    }

    public function addStructure(string $structure): static
    {
        $this->structures[] = $structure;
        return $this;
    }

    public function entry(): static
    { 
        $this->entry = true;
        return $this;
    }
}
