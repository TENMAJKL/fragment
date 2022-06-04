<?php

namespace Majkel\Fragment;

enum TokenKind
{
    case FunctionCall;
    case FunctionDefinition;
    case FunctionName;
    case Variables;
    case Variable;
    case Type;
    case Int;
    case String;
    case Array;
    case Void;
    case Bool;
    case Entry;
    case Any;
}
