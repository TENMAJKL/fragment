<?php

declare(strict_types=1);

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
        $result = [];

        /** @var \Majkel\Fragment\Token $token */
        $variables = null;
        $curent = '';
        while ($item < strlen($this->code) - 1) {
            ++$item;
            $char = $this->code[$item];
            if (PHP_EOL == $char) {
                if ($in_comment) {
                    $in_comment = false;
                }
                ++$this->line;
            }

            if ($in_string) {
                $curent .= $char;
                if ('\'' == $char) {
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
                    $this->tokens->push(new Token(TokenKind::FunctionCall, $curent, [], $this->line));
                    $curent = '';

                    break;

                case '}':
                    if (strlen($curent) > 0) {
                        $kind = $this->getKind($curent);
                        $this->tokens->top()->addChild(new Token($kind, $curent, [], $this->line));
                        $curent = '';
                    }

                    $last = $this->tokens->pop();

                    if ($this->tokens->empty()) {
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
                    if (0 === strlen($curent)) {
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

                    if ('--' == $curent) {
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

        if (count(array_filter(str_split($target), fn ($item) => is_numeric($item))) == strlen($target)) {
            return TokenKind::Int;
        }

        if (!$this->tokens->empty() && 'f' == $this->tokens->top()->content && empty($this->tokens->top()->children())) {
            return TokenKind::FunctionName;
        }

        if (in_array($target, ['int', 'string', 'void', 'bool'])) {
            return TokenKind::Type;
        }

        if ($target === 'null') {
            return TokenKind::Null; 
        }

        return TokenKind::Variable;
    }
}
