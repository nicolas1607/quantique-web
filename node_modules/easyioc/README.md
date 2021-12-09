Easyioc.js
==========

Stupidly simple _Inversion of Control_ for node.js apps.

Belongs to the Encore framework project:
[encore.jit.su](http://encore.jit.su) | [github.com/rm-rf-etc/encore](http://github.com/rm-rf-etc/encore)  

[![NPM](https://nodei.co/npm/easyioc.png?downloads=true)](https://nodei.co/npm/easyioc/)


## Description

Easyioc is a container. You add modules to the container. Those modules receive their dependencies
automatically from the container when exec() is invoked.

```js
// simplified example

var ioc = require('easyioc')
var example = { foo:'bar' }

// Add the example object, but name it "models".
ioc
.add('models', example)
.add('app', app)
.exec()

function app(models){ // 'app' requires 'models'
    console.log(models)
    // prints: { foo: 'bar' }
}
```

Detects and explains circular dependencies: `[Error: Dependency circle encountered: Mod_B  ➤  Mod_C  ➤  Mod_A  ➤  Mod_B]`

Alerts for missing dependencies: `Error: Module "a" requires unknown dependency "thing".`

Because you can specify any function as a dependency, you can easily adapt any existing
modules (lodash, express, etc) to suit your use.

Use add() to include any of the following:
* any function
* any object
* any valid string for require()
* or any array of these

## Usage

```js
// moduleA.js
module.exports = function(){
    return 1+2
}
```
```js
// moduleB.js
module.exports = function(){
    return 'A'+'B'
}
```
```js
// main.js
module.exports = function(A, B){
    console.log(A, B)
}
```
```js
// start.js
var ioc = require('easyioc')
ioc
.add( 'main', __dirname+'/main' )
.add( 'A', __dirname+'/moduleA' )
.add( 'B', __dirname+'/moduleB' )
.exec()

// use __dirname if require gets lost
```

The first argument (optional) is the name you are giving the module which all other
modules will use to require it. Easyioc.add('redis') will add a module named 'redis'
which equals `require('redis')`.

Arrays are intended for adding many files which don't need to be required by other
modules. However, if you provide a name, then the resulting module will be an array
containing the objects and/or functions and/or `require` results from the array you
provided.

Easyioc works really well with filefetcher ([git](http://github.com/rm-rf-etc/filefetcher))
([npm](http://npmjs.org/package/filefetcher)). Together, they can automatically load your
entire project, very simply, and according to your own specs.


## Recursive Design

Easyioc also supports providing itself as a dependency, which allows subsequent modules to
add even more modules and execute once more.
```js
var easyioc = require('easyioc')

function app(ioc){

    function bestModuleEvar(_){
        console.log( _(process).has('exit') )
    }
    ioc
        .add( bestModuleEvar )
        .add( '_', 'lodash-node' )
        .exec()
}

easyioc
    .add(  app  )
    .add( 'ioc', easyioc  )
    .exec()
```

## Sequential Loading
If you need to load data into a module before other modules are to run,
you can invoke exec() to control the execution order.

```js
// fileA.js
module.exports = function(models){
    models.modelA = {property: 'some data'}
    models.modelB = {property: 'some data'}
}
```
```js
// fileB.js
module.exports = function(models){
    models.modelC = {property: 'some data'}
    models.modelD = {property: 'some data'}
}
```

So lets say we have these ^ files, and we need to have the data in these objects loaded so we can use
them in other files.

```js
var easyioc = require('easyioc')

easyioc
    .add( 'models', {} )
    .add( ['fileA', 'fileB'] )
    .exec()
    .add( 'controllers' )
    .exec()
// Assume our controllers make reference to the models we created above.
```

## How To Run The Tests

```
$ git clone http://github.com/rm-rf-etc/easyioc.git
$ cd easyioc
$ npm install expect.js
$ npm install -g mocha
$ mocha
```

## License
The MIT License (MIT)

Copyright (c) 2013 Rob Christian

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
