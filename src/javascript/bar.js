moonGalleryControllers.controller('BarCtrl', ["$scope", "$http", "$location",
    function($scope, $http, $location) {
        refreshLogin();

        function refreshLogin() {
            $http.get('service.php?service=login&status=true')
                    .success(function(data) {
                        var loggedIn = data.loggedIn;
                        if (loggedIn) {
                            $scope.loggin = loggedIn;
                        } else {
                            $scope.loggin = null;
                        }
                    });
        }
    }
]);
