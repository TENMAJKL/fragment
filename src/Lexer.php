<?php

namespace Majkel\Fragment;

class Lexer
{
    private int $line = 1;

    private Stack $tokens;

    public function __construct(
        private string $code
    ) {
        $this->tokens = new Stack(); 
    }

    public function lex(): array
    {
        $item = -1;
        $in_string = false;
        $in_comment = false;
        $in_function = false;
        $result = [];
        /** @var \Majkel\Fragment\Token $token */
        $variables = null;
        $curent = '';
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
                    if (!$this->tokens->empty() && $this->tokens->top()->kind == TokenKind::FunctionDefinition && count($this->tokens->top()->children()) === 1) {
                        $variables = new Token(TokenKind::Variables, '', [], $this->line);
                        break;
                    }
                    if ($curent == 'f') {
                        $curent = '';
                        if ($in_function) {
                            throw new CompilerException('Function can\'t be defined in other function');
                        }
                        $in_function = true;
                        $this->tokens->push(new Token(TokenKind::FunctionDefinition, '', [], $this->line));
                        break;
                    }
                    $this->tokens->push(new Token(TokenKind::FunctionCall, $curent, [], $this->line));
                    $curent = '';

                    break;
                case '}':
                    if ($variables) {
                        if ($curent) {
                            $variables->addChild(new Token(TokenKind::Variable, $curent, [], $this->line)); 
                        }
                        $this->tokens->top()->addChild($variables);
                        $variables = null;
                        $curent = '';
                        break;
                    }
                    if ($curent) {
                        $kind = $this->getKind($curent);
                        $this->tokens->top()->addChild(new Token($kind, $curent, [], $this->line));
                        $curent = '';
                    }

                    if (!$in_function) {
                        throw new CompilerException('Unexpected code outside of function at line '.$this->line);
                    }
                    
                    $last = $this->tokens->pop();

                    if ($this->tokens->empty()) {
                        $in_function = false;
                        $result[] = $last;
                    } else {
                        $this->tokens->top()->addChild($last);
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
                    $this->tokens->top()->addChild(new Token($kind, $curent, [], $this->line));
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

        if (!$this->tokens->empty() &&$this->tokens->top()->kind == TokenKind::FunctionDefinition && empty($this->tokens->top()->children())) {
            return TokenKind::FunctionName;
        }

        if (in_array($target, ['int', 'string', 'void'])) {
            return TokenKind::Type;
        }
    
        return TokenKind::Variable;
    }
}
