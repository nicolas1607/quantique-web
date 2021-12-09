
/*!
 * Runway Router
 * Copyright(c) 2014 Rob Christian
 * MIT Licensed
 */


/**
 * Dependencies
 */
var _ = require('lodash-node')
var fs = require('fs')

/**
 * Runway private data
 */
var hash = require('crypto').createHash('md5')
var bytes = fs.readFileSync(__dirname+'/encore.ico').toJSON()
_.each(bytes, function(e){
    hash.update(e.toString())
})
var icon = {
    body: new Buffer( bytes ),
    headers: {
        'Content-Type': 'image/x-icon'
    ,   'Content-Length': bytes.length
    ,   'ETag': '"' + hash.digest() + '"'
    ,   'Cache-Control': 'public, max-age=' + 86400
    }
}
var routes_tree = Object.create(null)
var logger = undefined
var wildcards = [
    { card: '{int}', pattern: '([1-9][0-9]*)'     },
    { card: '{any}', pattern: '([0-9a-zA-Z-_.]+)' },
    { card: '{a-z}', pattern: '([a-zA-Z]+)'       },
    { card: '{num}', pattern: '([0-9]+)'          }
]



/**
 * The router API.
 */
function router(){

    add.group = group
    add.config = config
    add.group.config = config

    // c is for controller, f is for filters.
    function add(url, f, c){
        var Ω, nested

        if (typeof url !== 'string')
            throw new Error('Router accepts only a string as the first argument.')

        c = arguments[arguments.length-1]
        f = (_.isArray(f)) ? f : []

        if (!_.isFunction(c)) throw new Error('Controller either not specified or invalid.')
        _.each(f,function(e){
            if (!_.isFunction(e)) throw new Error('Filter is not a function: '+{}.toString.apply(e))
        })

        Ω = [].concat(f).concat(c)

        if (logger && _.isFunction(logger))
            logger('add route:', url, 'filters and controller:', Ω)
        // Convert route string into array of path segments.
        url = url.replace(/(^\/|\/$)/g,'').split('/')
        url[url.length] = 'Ω'
        nested = newBranch(url, Ω)

        // Now include the new route in our routes map object.
        _.merge(routes_tree, nested, function(a,b){
            var arr = a || b
            if (_.isArray(a))
                return _.uniq(a.concat(b), function(x){if (x) return x.toString()})
            else if (_.isArray(b))
                return b
        }) // Arrays are used for storing any segments which contain regex.

        return add
    }

    function group(base_url, f, c){
        c = arguments[arguments.length-1]
        f = (_.isArray(f)) ? f : undefined
        c = (_.isFunction(c)) ? c : undefined

        base_url = base_url.replace(/(^\/|\/$)/g,'')

        function sub_route(new_sub_route, f2, c2){
            new_sub_route = new_sub_route.replace(/(^\/|\/$)/g,'')

            c2 = arguments[arguments.length-1]
            c2 = (_.isFunction(c2)) ? c2 : c
            f2 = (_.isArray(f2)) ? f2 : f

            if (!c2)
                throw new Error('Controller not specified.')
            if (f2)
                add.apply(null, [base_url+'/'+new_sub_route, f2, c2])
            else
                add.apply(null, [base_url+'/'+new_sub_route, c2])

            return sub_route
        }
        sub_route.endgroup = add
        sub_route.group = group
        sub_route.config = config
        
        return sub_route
    }

    return add.apply(null, arguments)
}
router.config = config



/**
 * Configuration API.
 */
function config(name, obj){

    if (_.isString(name)) {

        switch (name) {

        case 'error':
            // Override default 404 response function.
            if (_.isFunction(obj)) sendError = obj
            break

        case 'logger':
        case 'logging':
            // Provide a callback to use for logging. Change to null/false/undefined to disable.
            if (_.isFunction(obj)) logger = obj
            break

        case 'favicon':
            // Override default favicon request handler.
            if (_.isString(obj) && require.resolve(obj)) {
                bytes = fs.readFileSync( obj ).toJSON()
                hash = require('crypto').createHash('md5')
                _.each(bytes, function(e){
                    hash.update(e.toString())
                })
                icon = {
                    body: new Buffer( bytes ),
                    headers: {
                        'Content-Type': 'image/x-icon'
                    ,   'Content-Length': bytes.length
                    ,   'ETag': '"' + hash.digest() + '"'
                    ,   'Cache-Control': 'public, max-age=' + 86400
                    }
                }
            }
            break

        case 'wildcard':
        case 'wildcards':
            // Add new wild card expressions.
            if (_.isArray(obj)) {
                wildcards = _(wildcards).concat(obj).where(function(obj){
                    return obj.card && obj.pattern && _.isString(obj.card) && _.isString(obj.pattern)
                }).value()
            }
            break
        }
    }

    return router
}



// Default failure handler (when no matching route is found). Use config() to override.
function sendError(code, req, res, args, ops){
    res.end(code)
}
// Default failure handler (when no matching route is found). Use config() to override.
function favicon(req, res, args, ops){
    res.writeHead( 200, icon.headers )
    res.end( icon.body )
}
/**
 * This is the node HTTP request listener. It will try to match the requested URL
 * and invoke the associated controller, or otherwise invoke error(), which calls
 * req.end('404') as the default. But you can override the error handler using
 * config().
 */
router.listener = function(req, res){

    var Ω, ops, route, args, norm, regs, redirect, i, n
    args = []
    n = 0

    if (req.url === '/favicon.ico')
        return favicon(req,res)

    ops = {
        i_redirect: function(fn){ // replace the controller with a different one.
            Ω[Ω.length-1] = fn
        },
        redirect: function(url){ // immediately respond with a 302 redirect.
            res.writeHead(302, {'Location': url})
            res.end()
        },
        error: function(code){
            sendError(code,req,res)
        }
    }

    // Convert route into array of URL segments, ending with "Ω", the leaf node.
    route = req.url.slice(1).replace(/\/$/g,'').split('?')[0].split('/')
    route[route.length] = 'Ω'

    // Climb the routes map, always check first for a matching static route segment before trying regex.
    Ω =  _.reduce(route, function(obj, seg){
        if (!obj)
            return

        norm = obj[seg] || undefined
        if (norm)
            return norm
        
        regs = obj['{regex}'] || undefined
        if (regs) {
            for (i=0; i < regs.length; i++) {
                if (regs[i].test(seg)) {
                    args[n++] = regs[i].exec(seg)[1] // Increments n after the value is used for the assignment. More performant than .push().
                    return obj[regs[i].toString()]
                }
            }
        }
    }, routes_tree) // <-- This is the object to climb.

    i = -1
    if (Ω) {
        // Execute in order, each function stored in the leaf node. (note: Ω[i++] != Ω[++i])
        (function next(){
            i++
            if (Ω[i])
                return Ω[i](req, res, args, ops, next)
            else
                return sendError('404', req, res, args, ops)
        })()
    }
    else sendError('404', req, res, args, ops)
}



/**
 * Helpers
 */
// Swap keys for values in a given string and return it as a regular expression.
function toRegExp(string){
    _(wildcards).each(function(e){
        string = string.replace(e.card, e.pattern)
    })
    
    return new RegExp(string)
}
// A branch is a series of nested objects.
function newBranch(array, fn){
    return _.reduce(array.reverse(), function(cumulate, segment){
        var x = Object.create(null)
        if (/\{...\}/g.test(segment)) {
            var re = toRegExp(segment)
            x['{regex}'] = [re]
            x[re.toString()] = cumulate
            return x
        } else {
            x[segment] = cumulate
            return x
        }
    }, fn)
}



/**
 */
module.exports = router
