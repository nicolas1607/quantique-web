var fs = require('fs')
var pth = require('path')
var filesHereSync = fs.readdirSync
var formPath = pth.join
var fullPath = pth.resolve
function isFileSync (path) { return fs.lstatSync( path ).isFile() }
function isFolderSync (path) { return fs.lstatSync( path ).isDirectory() }

module.exports = function filefetcher (criteria) {

    for (var e in criteria) {
        var o = criteria[e]
        if (isFolderSync(o.path))
            findMeMaybe(o.path, o.recursive, o.type, o.cb)
    }

    function findMeMaybe (path, recursive, ext, cb) {
        var type = new RegExp('[.]'+ext+'$')
        var files_here = filesHereSync(path)
        var more_folders = []
        var more_paths = []


        for (var i in files_here) {
            var new_file = files_here[i]

            if (new_file[0] !== '.') {
                var new_file = formPath(path, files_here[i])

                if ( isFileSync(new_file) && type.test(new_file) ) {

                    if ( typeof cb === 'function' )
                        cb( fullPath(new_file) )
                }

                if (recursive)
                    if (isFolderSync(new_file)) more_folders.push(new_file)
            }
        }
        for (var i in more_folders) // more_folders is empty if recursive is false
            findMeMaybe(more_folders[i], recursive, ext, cb)
    }
}
