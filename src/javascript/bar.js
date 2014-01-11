moonGalleryControllers.controller('BarCtrl', ["$scope", "$location", "moonGalleryServices",
    function($scope, $location, services) {
        $scope.signOut = signOut;

        services.addListener("loginChange", loginChange);

        function signOut() {
            services.load("logout").success(function(data) {
                $location.path("/galleries");
            });
        }

        function loginChange(login) {
            $scope.loggin = login;
        }
    }
]);
