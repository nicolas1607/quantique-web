
var fs = require('fs')

/**
 */
function isFolder_S (path) { return fs.lstatSync( path ).isDirectory() }

/**
 */
function isFile_S (path) { return fs.lstatSync( path ).isFile() }

/**
 */
function filesHere_S (path) { return fs.readdirSync( path ) }

/**
 */
function readFile_S (file, encoding)
{
    var encode = (isString(encoding)) ? encoding : 'utf8'
    if (isFile_S(file))
        return fs.readFileSync(file, encoding)
    else
        return null
}

exports.isFolder_S  = isFolder_S
exports.isFile_S    = isFile_S
exports.filesHere_S = filesHere_S
exports.readFile_S  = readFile_S
