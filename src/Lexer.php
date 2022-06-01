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
        $curent = '';
        /** @var \Majkel\Funktor\Token $token */
        $token = null;
        while ($item < strlen($this->code) - 1) {
            $item++;
            $char = $this->code[$item];
            if ($in_string) {
                $curent .= $char;
                continue;
            }
            switch ($char) {

                case PHP_EOL:
                    if ($in_comment) {
                        $in_comment = false;
                    }
                    $this->line++;
                    
                    break;
    
                if ($in_comment) {
                    break;
                }

                case '{':
                    $new = new Token(TokenKind::FunctionCall, $curent, [], $this->line);
                    $curent = '';

                    if ($token) {
                        $token->addChild($new);
                    } else {
                        $token = $new;
                    }

                    break;
                case '}':
                    $kind = $this->getKind($curent);
                    $token->addChild(new Token($kind, $curent, [], $this->line));
                    $curent = '';
                    $result[] = $token;
                    $token = null;

                    break;

                case '\'':
                    $curent .= '\'';
                    $in_string = !$in_string;

                    break;

                case ' ': 
                    if (!$curent) {
                        break;
                    }
                    $kind = $this->getKind($curent);
                    $token->addChild(new Token($kind, $curent, [], $this->line));
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

        throw new CompilerException('Undefined word '.$target.' at target '.$this->line);

    }
}
