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

    $scope.server = {
        qualif : { name : "qualif", show : true},
        preprod : {  name : "preprod", show : false},
        prod: {  name : "prod", show : false}
    };

    $scope.updating = 0;
    $scope.lastTag = { g:0, r:0, c:0 };
    $scope.tagTarget = { g: "G0", r: "R0", c: "C0" };
    $scope.running = {branch: 1, tag : 1, env: 1};

    $scope.currentEnv = $scope.server.qualif;
    $scope.currentEnvId = 0;
    $scope.branchList = [$scope.msg.loading];
    $scope.tagList = [$scope.msg.loading];
    $scope.current = $scope.msg.loading;
    $scope.envIds = [];
    $scope.fetchMsg = [];

    // targets pour changement de source
    $scope.target = "";
    $scope.tag = "";

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
        $scope.currentEnv = server;
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
        $scope.branchList = [$scope.msg.updating];

        getAllTags();

        // all branch names
        url = params.urls.branchAll+"/"+id;
        $http.get(url).success(function(data) {
            $scope.branchList = data;

            $scope.running.branch = 0;
            if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
        }).error(
            function() {
                alert("Oops! Branch");
                if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
            }
        );

        // get Env Ids
        url = params.urls.envId+"/"+id;
        $http.get(url).success(function(data) {
            $scope.envIds = data;
            $scope.getCurrentBranch();

            if ($scope.currentEnv == $scope.server.prod) {
                getLastTag();
            }

        }).error(
            function() {
                alert("Oops! Env");
                $scope.running.env = 0;
                if ($scope.running.tag == 0 && $scope.running.branch == 0) $scope.updating = 0;
            }
        );


    };

    var getAllTags =  function() {
        // all tags names
        var url = params.urls.tagAll+"/"+$scope.selected;
        $http.get(url).success(function(data) {
            $scope.tagList = data;

            $scope.running.tag = 0;
            if ($scope.running.branch == 0 && $scope.running.env == 0) $scope.updating = 0;
        }).error(
            function() {
                alert("Oops! tags");
                if ($scope.running.tag == 0 && $scope.running.env == 0) $scope.updating = 0;
            }
        );
    }

    $scope.getCurrentBranch = function() {

        $scope.updating = 1;
        $scope.current = $scope.msg.loading;

        for(var index in $scope.envIds) {
            var env = $scope.envIds[index];
            if (env.env.toLowerCase() == $scope.currentEnv.name.toLowerCase()) {
                $scope.currentEnvId = env.id;
                break;
            }
        }

        var url = params.urls.branchCurrent+"/"+$scope.currentEnvId;
        $http.get(url).success(function(data) {
            $scope.current = data.branch;
            $scope.target = data.branch;
            $scope.updating = 0;
        }).error(
            function() {
                $scope.updating = 0;
                alert("Oops! Env");
            }
        );
    };

    $scope.updateMe = function() {

        $scope.updating = 1;
        $scope.fetchMsg = [$scope.msg.loading];

        var url = params.urls.update+"/"+$scope.currentEnvId+"/"+$scope.current;
        $http.get(url).success(function(data) {
            $scope.fetchMsg = data;
            $scope.updating = 0;
        }).error(
            function() {
                $scope.updating = 0;
                alert("Oops! Env");
            }
        );
    };

    $scope.changeSource = function(changeBranch) {

        if ((changeBranch && $scope.target != "") || (!changeBranch && $scope.tag != "")) {

            $scope.updating = 1;
            $scope.fetchMsg = [$scope.msg.loading];

            var url = params.urls.change+"/"+$scope.currentEnvId+"/"+(changeBranch ? $scope.target : $scope.tag) ;
            $http.get(url).success(function(data) {
                $scope.fetchMsg = data;
                $scope.getCurrentBranch();
            }).error(
                function() {
                    alert("Oops! Env");
                    $scope.updating = 0;
                }
            );
        }
    };

    var getLastTag = function() {

        console.log("getLastTag");

        $scope.updating = 1;

        var url = params.urls.tagLast+"/"+$scope.currentEnvId;
        console.log("getLastTag: "+url);
        $http.get(url).success(function(data) {
            $scope.lastTag = data;
            updateTargetTag(data.g, data.r, data.c);
            $scope.updating = 0;
        }).error(
            function() {
                alert("Oops! Env");
                $scope.updating = 0;
            }
        );
    };

    $scope.tagTargetType = function(target) {
        $scope.newTagOk = 1;

        switch (target) {
            case "g":
                updateTargetTag((parseInt($scope.lastTag.g)+1), 0,0);
                break;
            case "r":
                updateTargetTag(
                    $scope.lastTag.g,
                    (parseInt($scope.lastTag.r)+1),
                    0
                );
                break;
            case "c":
                updateTargetTag(
                    $scope.lastTag.g,
                    $scope.lastTag.r,
                    (parseInt($scope.lastTag.c)+1)
                );
                break;
            case "x":
                updateTargetTag(
                    $scope.lastTag.g,
                    $scope.lastTag.r,
                    $scope.lastTag.c
                );
                $scope.newTagOk = 0;
                break;
        }
    }

    $scope.newTag = function() {

        var url = params.urls.tagNew
            +"/"+$scope.currentEnvId
            +"/"+$scope.tagTarget.g
            +"/"+$scope.tagTarget.r
            +"/"+$scope.tagTarget.c;
        console.log(url);
        //*
        $http.get(url).success(function(data) {
            $scope.fetchMsg = data;
            getLastTag();
            getAllTags();

        }).error(
            function() {
                alert("Oops! Env");
            }
        );
        //*/
    }

    var updateTargetTag = function(g,r,c) {
        $scope.tagTarget.g = g;
        $scope.tagTarget.r = r;
        $scope.tagTarget.c = c;
    }

    var resetTag = function() {
        $scope.lastTag = { g:0, r:0, c:0 };

        $scope.tagTarget.g = "x";
        $scope.tagTarget.r = "x";
        $scope.tagTarget.c = "x";
    }
}]);