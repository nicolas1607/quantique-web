
module.exports = function(methods){
	// var self = this

    function RESTController(i,o,a,r){
        var fn

        for (var j=0; j < RESTController.filters.length; j++) {
            fn = RESTController.filters[j]
            if (typeof fn === 'function')
                fn(i,o,a,r)
        }
        fn = RESTController[i.method.toLowerCase()]
        if (fn)
            fn(i,o,a,r)
        else
            r.error('404')
    }
    
    for (var p in methods)
        RESTController[p] = methods[p]

    RESTController.filters = (typeof methods.filters === 'object' && Array.isArray(methods.filters))
        ? methods.filters
        : []

    return RESTController
}
