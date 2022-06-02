<?php

namespace Majkel\Funktor;

enum TokenKind
{
    case FunctionCall;
    case FunctionDefinition;
    case Variables;
    case Variable;
    case Type;
    case Int;
    case String;
    case Array;
    case Void;
    case Entry;
}
