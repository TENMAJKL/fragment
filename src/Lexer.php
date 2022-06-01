<?php

namespace Majkel\Funktor;

class Lexer
{
    private int $line = 1;

    public function __construct(
        private string $code
    ) {
        
    }

    public function lex(): array
    {
        $item = -1;
        $in_string = false;
        $in_comment = false;
        $result = [];
        /** @var \Majkel\Funktor\Token $token */
        $variables = null;
        $curent = '';
        /** @var array<\Majkel\Funktor\Token> $tokens */
        $tokens = [];
        while ($item < strlen($this->code) - 1) {
            $item++;
            $char = $this->code[$item];
            if ($char == PHP_EOL) {
                if ($in_comment) {
                    $in_comment = false;
                }
                $this->line++;
            }

            if ($in_string) {
                $curent .= $char;
                if ($char == '\'') {
                    $in_string = false;
                }
                continue;
            }

            if ($in_comment) {
                continue;
            }
            switch ($char) {
                case PHP_EOL:
                    
                    break; 

                case '{':
                    if ($tokens && $tokens[count($tokens) - 1]->kind == TokenKind::FunctionDefinition && count($tokens[count($tokens) - 1]->children()) === 1) {
                        $variables = new Token(TokenKind::Variables, '', [], $this->line);
                        break;
                    }
                    if ($curent == 'f') {
                        $curent = '';
                        if ($tokens) {
                            throw new CompilerException('Function can\'t be defined in other function, in order to create lambda function use `lambda{`');
                        }
                        $tokens[] = new Token(TokenKind::FunctionDefinition, '', [], $this->line);
                        break;
                    }
                    $tokens[] = new Token(TokenKind::FunctionCall, $curent, [], $this->line);
                    $curent = '';

                    break;
                case '}':
                    if ($variables) {
                        $variables->addChild(new Token(TokenKind::Variable, $curent, [], $this->line)); 
                        $tokens[count($tokens) - 1]->addChild($variables);
                        $variables = null;
                        $curent = '';
                        break;
                    }
                    if ($curent) {
                        $kind = $this->getKind($curent);
                        $tokens[count($tokens) - 1]->addChild(new Token($kind, $curent, [], $this->line));
                        $curent = '';
                    }
                    $last = array_pop($tokens);
                    if (empty($tokens)) {
                        $result[] = $last;
                    } else {
                        $tokens[count($tokens) - 1]->addChild($last);
                    }

                    break;

                case '\'':
                    $curent = '\'';
                    $in_string = true;

                    break;

                case ' ': 
                    if (!$curent) {
                        break;
                    }
                    if ($variables) {
                        $variables->addChild(new Token(TokenKind::Variable, $curent, [], $this->line)); 
                        $curent = '';
                        break;
                    }

                    $kind = $this->getKind($curent);
                    $tokens[count($tokens) - 1]->addChild(new Token($kind, $curent, [], $this->line));
                    $curent = '';
                    break;

                default:
                    $curent .= $char;

                    if ($curent == '--') {
                        $in_comment = true;
                        $curent = '';
                    }
            }
        }

        if ($in_string) {
            throw new CompilerException('Unclosed string at line '.$this->line);
        }

        return $result;
    }

    public function getKind(string $target): TokenKind
    {
        if (str_starts_with($target, '\'') && str_ends_with($target, '\'')) {
            return TokenKind::String;
        }

        if (count(array_filter(str_split($target), fn($item) => is_numeric($item))) == strlen($target)) {
            return TokenKind::Int;
        }

        if (in_array($target, ['int', 'string', 'void'])) {
            return TokenKind::Type;
        }
    
        return TokenKind::Variable;
    }
}
