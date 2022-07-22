<?php

declare(strict_types=1);

namespace Majkel\Fragment;

enum TokenKind
{
    case FunctionCall;

    case FunctionName;

    case Arguments;

    case Variable;

    case Type;

    case Int;

    case String;

    case Array;

    case Void;

    case Bool;

    case Pair;

    case Entry;

    case Any;
}
