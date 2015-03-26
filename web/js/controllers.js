var deployApp = angular
    .module('deployApp', [])
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

    $scope.currentEnv = $scope.server.qualif;
    $scope.currentEnvId = 0;
    $scope.branchList = ["loading"];
    $scope.tagList = ["loading"];
    $scope.current = $scope.msg.loading;
    $scope.envIds = [];
    $scope.fetchMsg = [];

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
    }

    $scope.select = function(id) {

        $scope.selected = id;
        $scope.branchList = [$scope.msg.updating];

        var urlAll = params.urls.branchAll+"/"+id;
        $http.get(urlAll).success(function(data) {
            $scope.branchList = data;
        }).error(
            function() {
                alert("Oops! Branch");
            }
        );

        // get Env Ids
        var urlCurrent = params.urls.envId+"/"+id;
        $http.get(urlCurrent).success(function(data) {
            $scope.envIds = data;
            $scope.getCurrentBranch();
        }).error(
            function() {
                alert("Oops! Env");
            }
        );
    }

    $scope.getCurrentBranch = function() {

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
        }).error(
            function() {
                alert("Oops! Env");
            }
        );
    }

    $scope.updateMe = function() {

        $scope.fetchMsg = [$scope.msg.loading];
        //$scope.current = $scope.msg.updating;

        var url = params.urls.update+"/"+$scope.currentEnvId+"/"+$scope.current;
        $http.get(url).success(function(data) {
            $scope.fetchMsg = data;
        }).error(
            function() {
                alert("Oops! Env");
            }
        );
    }


}]);