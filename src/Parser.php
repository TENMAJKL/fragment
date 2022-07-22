<?php

declare(strict_types=1);

namespace Majkel\Fragment;

use Majkel\Fragment\Functions\Args;
use Majkel\Fragment\Functions\BooleanOperators;
use Majkel\Fragment\Functions\Chain;
use Majkel\Fragment\Functions\Conditions;
use Majkel\Fragment\Functions\Definition;
use Majkel\Fragment\Functions\Head;
use Majkel\Fragment\Functions\IsNull;
use Majkel\Fragment\Functions\Operators;
use Majkel\Fragment\Functions\Output;
use Majkel\Fragment\Functions\Pair;
use Majkel\Fragment\Functions\Range;
use Majkel\Fragment\Functions\Stick;
use Majkel\Fragment\Functions\Tail;
use Majkel\Fragment\Functions\UserFunction;

class Parser
{
    public const TypesKind = [
        'string' => TokenKind::String,
        'int' => TokenKind::Int,
        'bool' => TokenKind::Bool,
    ];

    public bool $in_function = false;
    private array $functions = [];

    private array $structures = [
        'World' => [
            'out' => TokenKind::Array,
        ],
        'Pair' => [
            'head' => TokenKind::Any,
            'tail' => 'Pair',
        ],
    ];

    private array $internal_functions = [
        'end' => [['world'],
            ['world.out.forEach((item) => console.log(item))'],
        ],
    ];

    private array $variables = [];

    private bool $entry = false;

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

    public function getInternalFunctions(): array
    {
        return $this->internal_functions;
    }

    public function addInternalFunction(string $name, array $args, array $lines): self
    {
        $this->internal_functions[$name] = [$args, $lines];

        return $this;
    }

    public function parseToken(Token $token): Result
    {
        if (!$this->in_function && TokenKind::FunctionName !== $token->kind && 'f' !== $token->content) {
            throw new CompilerException('Unexpected code outside of function at line '.$token->line);
        }

        return match ($token->kind) {
            TokenKind::Int => new Result([$token->content], TokenKind::Int),
            TokenKind::String => new Result(['"'.substr($token->content, 1, -1).'"'], TokenKind::String),
            TokenKind::FunctionCall => $this->parseFunctionCall($token),
            TokenKind::Type => $this->parseType($token),
            TokenKind::Variable => $this->parseVariable($token),
            TokenKind::FunctionName => new Result([$token->content], TokenKind::FunctionName),
            TokenKind::Null => new Result(['null'], TokenKind::Null),
        };
    }

    public function parseType(Token $token): Result
    {
        if (!isset(self::TypesKind[$token->content])) {
            throw new CompilerException('Type '.$token->content.' does not exist');
        }

        return new Result([$token->content], TokenKind::Type);
    }

    public function removeVariables(Token $token): static
    {
        foreach ($token->children() as $child) {
            unset($this->variables[explode(':', $child->content)[0]]);
        }

        return $this;
    }

    public function addVariable(string $name, string $type): static
    {
        $this->variables[$name] = $this->getType($type);

        return $this;
    }

    public function getType(string $type): TokenKind|string
    {
        if (isset(Parser::TypesKind[$type])) {
            return Parser::TypesKind[$type];
        }
        if (isset($this->structures[$type])) {
            return $type;
        }

        throw new CompilerException('Unknown type: '.$type);
    }

    public function parseVariable(Token $token): Result
    {
        $name = $token->content;
        if (isset($this->structures[$name])) {
            return new Result([$name], TokenKind::Type);
        }
        if (!isset($this->variables[$name])) {
            throw new CompilerException('Variable '.$name.' does not exist');
        }

        return new Result([$name], $this->variables[$name]);
    }

    public function parseFunctionCall(Token $token): Result
    {
        return (new (match ($token->content) {
            'f' => Definition::class,
            'args' => Args::class,
            '+', '-', '/', '*', '%' => Operators::class,
            '==', '>', '<', '>=', '<=' => BooleanOperators::class,
            'if' => Conditions::class,
            'echo' => Output::class,
            'o-o' => Chain::class,
            'pair' => Pair::class,
            'head' => Head::class,
            'tail' => Tail::class,
            '?' => IsNull::class,
            '..' => Range::class,
            '~' => Stick::class,
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

    public function addStructure(string $name, array $struct): static
    {
        $this->structures[$name] = $struct;

        return $this;
    }

    public function getStructure(string $name): array
    {
        return $this->structures[$name];
    }

    public function newStructure(string $name): string
    {
        $struct = $this->getStructure($name);
        return json_encode(array_combine(array_keys($struct), array_map(fn($item) => $item === TokenKind::Array ? [] : 'null', $struct)));
    }

    public function entry(): static
    {
        $this->entry = true;

        return $this;
    }

    public function structures(): array
    {
        return $this->structures;
    }
}
