/*
 * Copyright (C) 2014 Martin Indra <martin.indra at mgn.cz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

moonGalleryControllers.controller('SignUpCtrl', ["$scope", "moonGalleryServices",
    function($scope, services) {
        var previousFullName = null,
                aliasByFullName = true;

        $scope.signUp = signUp;

        $scope.$watch("fullName", function(value) {
            if (aliasByFullName && $scope.alias == previousFullName) {
                $scope.alias = value;
            } else {
                aliasByFullName = false;
            }
            previousFullName = value;
        });

        function signUp() {
            var password = $scope.password;
            $scope.password = null;

            services.load("register", {
                email: $scope.email,
                password: password,
                alias: $scope.alias,
                full_name: $scope.fullName
            }).success(function(data) {
                data = data.service;

                if (data === true) {
                    $scope.error = null;
                    $scope.registered = true;
                } else if (data.error) {
                    switch (data.error) {
                        case "wrong-alias":
                            $scope.error = "Wrong alias!";
                            break;
                        case "email-exists":
                            $scope.error = "This e-mail is already registered!";
                            break;
                        case "wrong-email":
                            $scope.error = "Wrong e-mail!";
                            break;
                        case "wrong-full-name":
                            $scope.error = "Wrong full name!";
                            break;
                        case "password-too-soft":
                            $scope.error = "Password is too soft, use longer!";
                            break;
                        case "wrong-password":
                            $scope.error = "Wrong password!";
                            break;
                        case "unspecified-error":
                            $scope.error = "Filled values are not correct!";
                            break;
                    }
                } else {
                    $scope.error = "An error occured!";
                }
            });

        }
    }
]);
