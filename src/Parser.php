<?php

namespace Majkel\Fragment;

use Majkel\Fragment\Functions\Args;
use Majkel\Fragment\Functions\BooleanOperators;
use Majkel\Fragment\Functions\Conditions;
use Majkel\Fragment\Functions\Definition;
use Majkel\Fragment\Functions\Operators;
use Majkel\Fragment\Functions\UserFunction;

class Parser
{
    private array $functions = [];

    private array $structures = [];

    private array $variables = [];

    private bool $entry = false;

    public bool $in_function = false;

    public const Types = [
        'string' => 'char*', // TODO
        'int' => 'int',
    ];

    public const TypesKind = [
        'string' => TokenKind::String,
        'int' => TokenKind::Int
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
        if (!$this->in_function && $token->kind !== TokenKind::FunctionName && $token->content !== 'f') {
            throw new CompilerException('Unexpected code outside of function at line '.$token->line);
        }
        return match ($token->kind) {
            TokenKind::Int => new Result([$token->content], TokenKind::Int),
            TokenKind::String => new Result(['"'.substr($token->content, 1, -1).'"'], TokenKind::String),
            TokenKind::FunctionCall => $this->parseFunctionCall($token),
            TokenKind::Type => $this->parseType($token),
            TokenKind::Variable => $this->parseVariable($token), 
            TokenKind::FunctionName => new Result([$token->content], TokenKind::FunctionName),
        };
    }

    public function parseType(Token $token): Result
    {
        if (!isset(self::Types[$token->content])) {
            throw new CompilerException('Type '.$token->content.' does not exist');
        }

        return new Result([self::Types[$token->content]], TokenKind::Type);
    }

    public function removeVariables(Token $token): static
    {
        foreach ($token->children() as $child) {
            unset($this->variables[explode(':', $child->content)[0]]);
        }
        return $this;
    }

    public function addVariable(string $name, TokenKind $type): static
    {
        $this->variables[$name] = $type;
        return $this;
    }

    public function parseVariable(Token $token): Result
    {
        $name = $token->content;
        if (!isset($this->variables[$name])) {
            throw new CompilerException('Variable '.$name.' does not exist');
        }
        return new Result([$name], $this->variables[$name]);
    }

    public function parseFunctionCall(Token $token): Result
    {
        return (new (match($token->content) {
            'f' => Definition::class,
            'args' => Args::class,
            '+', '-', '/', '*' => Operators::class,
            '==', '>', '<', '>=', '<=' => BooleanOperators::class,
            'if' => Conditions::class,
            default => UserFunction::class,
        })($token, $this))->compile();
    }

    public function parseUserFunction(Token $token): Result
    {
        if (!isset($this->functions[$token->content])) {
            throw new CompilerException('Function '.$token->content.' does not exist');
        }

        return new Result([$token->content.'('.$this->parse($token->children()).')'], $this->functions[$token->content]->children()[2]);
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

    public function getFunction(string $function): Token
    {
        if (!isset($this->functions[$function])) {
            throw new CompilerException('Function '.$function.' does not exist');
        }
        return $this->functions[$function];
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
