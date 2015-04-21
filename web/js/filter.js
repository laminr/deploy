angular.module('deployFilters', [])
    .filter('trim', function() {
        return function(text) {
            return String(text).replace(/\s/mg, "");
        };
    })
;