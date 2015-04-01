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

    $scope.server = {
        qualif : { name : "qualif", show : true},
        preprod : {  name : "preprod", show : false},
        prod: {  name : "prod", show : false}
    };

    $scope.updating = 0;
    $scope.lastTag = { g:0, r:0, c:0 };
    $scope.tagTarget = { g: "G0", r: "R0", c: "C0" };

    $scope.currentEnv = $scope.server.qualif;
    $scope.currentEnvId = 0;
    $scope.branchList = [$scope.msg.loading];
    $scope.tagList = [$scope.msg.loading];
    $scope.current = $scope.msg.loading;
    $scope.envIds = [];
    $scope.fetchMsg = [];
    $scope.target = "";

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
            $scope.getLastTag();
        }
        $scope.currentEnv = server;
        $scope.getCurrentBranch();
    };

    $scope.select = function(id) {

        $scope.updating = 1;
        var running = {branch: 1, tag : 1, env: 1};
        resetTag();

        var url = "";
        // recupération de toutes les branches existantes
        $scope.selected = id;
        $scope.branchList = [$scope.msg.updating];

        // all branch names
        url = params.urls.branchAll+"/"+id;
        $http.get(url).success(function(data) {
            $scope.branchList = data;

            running.branch = 0;
            if (running.tag == 0 && running.env == 0) $scope.updating = 0;
        }).error(
            function() {
                alert("Oops! Branch");
                if (running.tag == 0 && running.env == 0) $scope.updating = 0;
            }
        );

        // all tags names
        url = params.urls.tagAll+"/"+id;
        $http.get(url).success(function(data) {
            $scope.tagList = data;

            running.tag = 0;
            if (running.branch == 0 && running.env == 0) $scope.updating = 0;
        }).error(
            function() {
                alert("Oops! tags");
                if (running.tag == 0 && running.env == 0) $scope.updating = 0;
            }
        );

        // get Env Ids
        url = params.urls.envId+"/"+id;
        $http.get(url).success(function(data) {
            $scope.envIds = data;
            $scope.getCurrentBranch();

            if ($scope.currentEnv == $scope.server.prod) {
                $scope.getLastTag();
            }

        }).error(
            function() {
                alert("Oops! Env");
                running.env = 0;
                if (running.tag == 0 && running.branch == 0) $scope.updating = 0;
            }
        );


    };

    $scope.getCurrentBranch = function() {

        $scope.updating = 1;
        $scope.current = $scope.msg.loading;

        for(var index in $scope.envIds) {
            var env = $scope.envIds[index];
            if (env.env.toLowerCase() == $scope.currentEnv.name.toLowerCase()) {
                $scope.currentEnvId = env.id;
                console.log("current:"+$scope.currentEnvId);
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

    $scope.changeSource = function() {

        if ($scope.target != "") {

            $scope.updating = 1;
            $scope.fetchMsg = [$scope.msg.loading];

            var url = params.urls.change+"/"+$scope.currentEnvId+"/"+$scope.target;
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

    $scope.getLastTag = function() {

        $scope.updating = 1;

        var url = params.urls.tagLast+"/"+$scope.currentEnvId;
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
        $scope.updating = 1;
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
                break;
        }

        $scope.updating = 0;
    }

    $scope.newTag = function() {

        var url = params.urls.tagNew+"/"+$scope.currentEnvId;
        $http.get(url).success(function(data) {
            $scope.lastTag = data;
        }).error(
            function() {
                alert("Oops! Env");
            }
        );
    }

    var updateTargetTag = function(g,r,c) {
        $scope.tagTarget.g = "G"+g;
        $scope.tagTarget.r = "R"+r;
        $scope.tagTarget.c = "C"+c;
    }

    var resetTag = function() {
        $scope.lastTag = { g:0, r:0, c:0 };

        $scope.tagTarget.g = "Gx";
        $scope.tagTarget.r = "Rx";
        $scope.tagTarget.c = "Cx";
    }
}]);