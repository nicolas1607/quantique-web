#!/usr/bin/env node

module.exports = MVC
MVC.settings = require('e_settings')
function MVC(app){
    var NODE_DOMAIN, fs, _
    // NODE_DOMAIN = require('domain').create()
    // NODE_DOMAIN.on('error', function (err) { console.log(err.message, err.stack) })
    // NODE_DOMAIN.run(function(){

    _ = require('lodash-node')
    _.chokidar = require('chokidar')
    fs = require('fs')

    _.extend(_, require('e_helpers_io'))
    _.extend(_, require('e_helpers_path'))
    _.extend(_, require('e_helpers_process'))
    _.extend(_, require('e_helpers_typecheck'))
    _.log = _.noop

    _.watch = function(file, cb){
        function reload(err, data){
            if (err) throw err
            _.log('file updated: '+file)
            cb(data)
        }
        _.chokidar
        .watch(file, {persistent: true})
        .on('change', function(){
            fs.readFile(file, reload)
        })
    }

    var easyioc = require('easyioc')
    var router = require('runway')

    function adapt(module){
        return (_.isString(module))
            ? function(){ return require(module) }
            : function(){ return module }
    }

    var server = require('http').createServer(router.listener)
    server.cert = function(cert){
        return require('https').createServer(cert, router.listener)
    }

    easyioc
        .add( '_',               adapt(_)               )
        .add( 'router',          adapt(router)          )
        .add( 'ioc',             adapt(easyioc)         )
        .add( 'fetchfiles',      adapt('filefetcher')   )
        .add( 'RESTController',  adapt('e_controllers') )
        .add( 'views',           'e_views'              )
        .add( 'settings',        MVC.settings           )
        .add( 'server',          server                 )
        .add( 'main',            app                    )
        .exec()
}
