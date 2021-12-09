
var fs = require('fs')
var pth = require('path')

/**
 * @method isFolder_S
 * @for Exports
 * @param path {String}
 * @return {Boolean}
 */
function isFolder_S (path) { return fs.lstat_S( path ).isDirectory() }

/**
 * @method isFile_S
 * @for Exports
 * @param path {String}
 * @return {Boolean}
 */
function isFile_S (path) { return fs.lstat_S( path ).isFile() }

/**
 * @method filesHere_S
 * @for Exports
 * @param path {String}
 * @return {Array}
 */
function filesHere_S (path) { return fs.readdir_S( path ) }

/**
 * @method loadFile_S
 * @for Exports
 * @param file {String}
 * @param encoding {String}
 * @return {String}
 */
function loadFile_S (file, encoding)
{
    var encode = (isString(encoding)) ? encoding : 'utf8'
    if (isFile_S(file))
        return fs.readFile_S(file, encoding)
    else
        return null
}
