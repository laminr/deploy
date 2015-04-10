var deployApp = angular
    .module('deployApp', ['deployFilters'])
    .config(
        function($interpolateProvider){
            $interpolateProvider.startSymbol('[[').endSymbol(']]');
        }
    );

deployApp.controller('DeployCtrl', ['$scope', '$http', function ($scope, $http) {

    $scope.msg = { loading: "<loading>", updating: "UPDATING" } ;
    $scope.selected = 0;
    $scope.newTagOk = 0;
    $scope.updating = 0;
    $scope.error = { has : false, message: ""};

    $scope.server = {
        qualif :  { name : "qualif", show : true},
        preprod : { name : "preprod", show : false},
        prod:     { name : "prod", show : false}
    };

    $scope.branch = {
        target : "",
        list : [$scope.msg.loading]
    };

    $scope.tag = {
        last : { g:0, r:0, c:0 },
        target : { g: "x", r: "x", c: "x" },
        list : [$scope.msg.loading],
        name : ""
    }

    $scope.running = {branch: 1, tag : 1, env: 1};

    $scope.current = {
        env     : $scope.server.qualif,
        envid   : 0,
        branch  : $scope.msg.loading,
        tag     : ""
    }

    $scope.envIds     = [];
    $scope.fetchMsg   = [];

    // targets pour changement de source


    $scope.showServer = function (server) {

        if(server.name == $scope.server.qualif.name) {
            $scope.server.qualif.show = true;
            $scope.server.preprod.show = false;
            $scope.server.prod.show = false;
        }
        else if(server.name == $scope.server.preprod.name) {
            $scope.server.qualif.show = false;
            $scope.server.preprod.show = true;
            $scope.server.prod.show = false;
        }
        else if(server.name == $scope.server.prod.name) {
            $scope.server.qualif.show = false;
            $scope.server.preprod.show = false;
            $scope.server.prod.show = true;
        }
        $scope.current.env = server;
        $scope.getCurrentBranch();

        // after getCurrentBranch to update current id
        if (server.name == $scope.server.prod.name) getLastTag();
    };

    $scope.select = function(id) {
        $scope.updating = 1;
        $scope.running = {branch: 1, tag : 1, env: 1};
        resetTag();

        var url = "";
        // recupération de toutes les branches existantes
        $scope.selected = id;
        $scope.branch.list = [$scope.msg.updating];

        getAllTags();
        getAllBranch();
        getEnvIds();

    };

    var getEnvIds = function () {
        // get Env Ids
        var url = params.urls.envId+"/"+$scope.selected;
        $http.get(url).success(function(data) {
            $scope.envIds = data;
            $scope.getCurrentBranch();
            if ($scope.current.env == $scope.server.prod) {
                getLastTag();
            }

        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: liste ID Env --> "+data;
                $scope.running.env = 0;
                if ($scope.running.tag == 0 && $scope.running.branch == 0) $scope.updating = 0;
            }
        );
    }

    var getAllBranch = function() {
        // all branch names
        var url = params.urls.branchAll+"/"+$scope.selected;
        $http.get(url).success(function(data) {
            $scope.branch.list = data;
            $scope.running.branch = 0;
            if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur lors de l'appel de la liste des branches";
                if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
            }
        );
    }

    var getAllTags =  function() {
        // all tags names
        var url = params.urls.tagAll+"/"+$scope.selected;
        $http.get(url).success(function(data) {
            $scope.tag.list = data;
            $scope.running.tag = 0;
            if ($scope.running.branch == 0 && $scope.running.env == 0) $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: getAllTags --> "+data;
                if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
            }
        );
    }

    $scope.getCurrentBranch = function() {

        $scope.updating = 1;
        $scope.current.branch = $scope.msg.loading;

        for(var index in $scope.envIds) {
            var env = $scope.envIds[index];
            if (env.env.toLowerCase() == $scope.current.env.name.toLowerCase()) {
                $scope.current.envid = env.id;
                break;
            }
        }

        var url = params.urls.branchCurrent+"/"+$scope.current.envid;
        $http.get(url).success(function(data) {
            $scope.current.branch = data.branch;
            $scope.branch.target = data.branch;
            $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: getCurrentBranch --> "+data;
                $scope.updating = 0;
            }
        );
    };

    $scope.getCurrentTag = function() {

        $scope.updating = 1;
        $scope.current.branch = $scope.msg.loading;

        for(var index in $scope.envIds) {
            var env = $scope.envIds[index];
            if (env.env.toLowerCase() == $scope.current.env.name.toLowerCase()) {
                $scope.current.envid = env.id;
                break;
            }
        }

        var url = params.urls.tagCurrent+"/"+$scope.current.envid;
        $http.get(url).success(function(data) {
            $scope.current.branch = data.branch;
            $scope.branch.target = data.branch;
            $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: getCurrentBranch --> "+data;
                $scope.updating = 0;
            }
        );
    };

    $scope.updateMe = function() {

        $scope.updating = 1;
        $scope.fetchMsg = [$scope.msg.loading];

        var url = params.urls.update+"/"+$scope.current.envid+"/"+$scope.current.branch;
        $http.get(url).success(function(data) {
            $scope.fetchMsg = data;
            $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: updateMe --> "+data;
                $scope.updating = 0;
            }
        );
    };

    $scope.changeSource = function(changeBranch) {

        if ((changeBranch && $scope.branch.target != "") || (!changeBranch && $scope.current.tag != "")) {

            $scope.updating = 1;
            $scope.fetchMsg = [$scope.msg.loading];
            $scope.current.branch = [$scope.msg.loading];

            var url = params.urls.change+"/"+$scope.current.envid+"/"+(changeBranch ? $scope.branch.target : $scope.tag.name) ;
            $http.get(url).success(function(data) {
                $scope.fetchMsg = data;
                $scope.getCurrentBranch();
            }).error(
                function(data, status, headers, config) {
                    $scope.error.has = true;
                    $scope.error.messsage = "Erreur: changeSource --> "+data;
                    $scope.updating = 0;
                }
            );
        }
    };

    var getLastTag = function() {
        $scope.updating = 1;

        var url = params.urls.tagLast+"/"+$scope.current.envid;

        $http.get(url).success(function(data) {
            $scope.current.tag = data;
            updateTargetTag(data.g, data.r, data.c);
            $scope.updating = 0;
        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: getLastTag --> "+data;
                $scope.updating = 0;
            }
        );
    };

    $scope.tagTargetType = function(target) {
        $scope.newTagOk = 1;

        switch (target) {
            case "g":
                updateTargetTag((parseInt($scope.tag.last.g)+1), 0,0);
                break;
            case "r":
                updateTargetTag(
                    $scope.tag.last.g,
                    (parseInt($scope.tag.last.r)+1),
                    0
                );
                break;
            case "c":
                updateTargetTag(
                    $scope.tag.last.g,
                    $scope.tag.last.r,
                    (parseInt($scope.tag.last.c)+1)
                );
                break;
            case "x":
                updateTargetTag(
                    $scope.tag.last.g,
                    $scope.tag.last.r,
                    $scope.tag.last.c
                );
                $scope.newTagOk = 0;
                break;
        }
    }

    $scope.newTag = function() {

        var url = params.urls.tagNew
            +"/"+$scope.current.envid
            +"/"+$scope.tag.target.g
            +"/"+$scope.tag.target.r
            +"/"+$scope.tag.target.c;

        $http.get(url).success(function(data) {
            $scope.fetchMsg = data;
            getLastTag();
            getAllTags();

        }).error(
            function(data, status, headers, config) {
                $scope.error.has = true;
                $scope.error.messsage = "Erreur: newTag --> "+data;
            }
        );
    }

    var updateTargetTag = function(g,r,c) {
        $scope.tag.target.g = g;
        $scope.tag.target.r = r;
        $scope.tag.target.c = c;

        $scope.tag.name = "TAG-"
            +"G"+$scope.tag.target.g
            +"R"+$scope.tag.target.r
            +"C"+$scope.tag.target.c;
    }

    var resetTag = function() {
        $scope.tag.last = { g:0, r:0, c:0 };

        $scope.tag.target.g = "x";
        $scope.tag.target.r = "x";
        $scope.tag.target.c = "x";

        $scope.tag.name = "";

    }
}]);