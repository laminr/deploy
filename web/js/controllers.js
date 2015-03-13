var deployApp = angular
    .module('deployApp', [])
    .config(
        function($interpolateProvider){
            $interpolateProvider.startSymbol('[[').endSymbol(']]');
        }
    );

deployApp.controller('DeployCtrl', ['$scope', '$http', function ($scope, $http) {

    $scope.projects = projects;
    $scope.selected = 0;

    $scope.server = {
        qualif : { name : "qualif", show : true},
        preprod : {  name : "preprod", show : false},
        prod: {  name : "prod", show : false}
    };

    $scope.branchList= ["loading"];
    $scope.tagList = ["loading"];

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
    }

    $scope.select = function(id) {
        $scope.selected = id;
    }
}]);