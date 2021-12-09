
var filefetcher = require('../filefetcher')
var expect = require('expect.js')


describe('filefetcher',function(){
    it('finds files in directories in test/',function(){
        var results = []
        function store (file) { results.push(file) }

        filefetcher([
            { path:__dirname+'/1', type:'js', recursive:false, cb:store },
            { path:__dirname+'/2', type:'js', recursive:false, cb:store },
            { path:__dirname+'/3', type:'js', recursive:true,  cb:store }
        ])

        var expected_output = [
            __dirname+'/1/1.js',
            __dirname+'/2/a.js',
            __dirname+'/2/b.js',
            __dirname+'/2/c.js',
            __dirname+'/3/z.js',
            __dirname+'/3/extra/extra.js'
        ]

        expect( results ).to.eql( expected_output )
        console.log( '\n', results )
    })
})
