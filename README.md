# Fragment

![](fragment.png)

Fragment is basicaly combination of lisp and clean maybe which compiles to js. Its written in php because its first bootstrap before it will be rewritten in it self (if ever).

## Doc

Like in lisp, everything is done as function, but here functions have the standart look:

```
function{argument1 argument2 etc}
```

Unlike most lisp implementations here you have to provide entry function which also makes it possible to write pure functional programs here.

To define function there is function `f` which takes name as first argument, argument list as second, return type as third and code as forth. 

Arguments are passed through function args.

Where entry function will allways look like this:

```
f{entry args{world:World} World
    your code
}

```


Entry function takes argument with type World and returns type World, reason for this is, that we can keep our app purely functional. Struct world basicaly just holds all values that will be printed and once the function ends, they will be all printed.

To output stuff we have function echo which takes any value as first argument (that will be printed) and world as second. It basicaly just changes the world (adds new printing variable) and returns new version of it.

Hello world:

```
f{entry args{world:World} World
    echo{'hello world' world}
}

```

Now lets see compiled js:

```js
// echos each value that has to be printed
function __end(world) {
    world.out.forEach((item) => console.log(item))
}

// pushes new value to the world and returns the world
function __echo(target, world) {
    world.out.push(target)
    return world
}

// at this point we are just calling end with our entry function
__end((function(world) {
    return __echo("hello world", world)
})({"out":[]}))
```

As we can see it just takes initial object and manipulates with it.

This topic is well described in [this video](https://www.youtube.com/watch?v=fCoQb-zqYDI)

If we want to use multiple echos, we can do that like so:

```

echo{10 echo{20 world}}

```

and it will output 20 10.

But this can get confusing so there is syntax sugar for it. `o-o` is similar to `>>=` in haskell.

You first provide value which will be input for first function, then every other function will use returned value of the previous state. Result of the last will be returned from this function.

```
o-o{
    world
    echo{'foo'}
    echo{'bar'}
}

-- is the same as

echo{'bar' echo{'foo' world}}
```

oh and `--` starts comment.

I like to say that its basicaly like unix pipe.

In fragment we can do programming stuff actualy:

Functions `+ - / * % == > < >= <=` works just the same as in js. You just have to use [polish notation](https://en.wikipedia.org/wiki/Polish_notation) like in lisp.

To do condition stuff there is function `if` which works like ternary operator in js:

```
if{=={1 2} 'foo' 'bar'}
```

If you want to work with arrays, you can use pair:

which is structure described like so:

```
{
    head: int,
    tail: {
        head: int
        tail: ...
    }
}
```

Its recursive so we dont need bloated loops and can use recursion like true mathematitians.

To create pair just:

```
pair{10 pair{20 null}}
```

it can go on obviously

```
head{pair{10 pair{20 null}}} -- 10

tail{pair{10 pair{20 null}}} -- {20 null}
```

To see if variable is null there is function `?`

```
?{null} -- true
?{'klobna'} -- false
```


Oh man if anyone can understand this language because of this he is master

## TODO

generics
