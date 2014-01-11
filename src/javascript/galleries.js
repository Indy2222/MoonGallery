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

moonGalleryControllers.controller('GalleriesCtrl', ["$scope", "$location", "moonGalleryServices",
    function($scope, $location, services) {
        $scope.galleries = [];
        $scope.refreshGalleries = refreshGalleries;
        $scope.showGallery = showGallery;

        refreshGalleries();

        function showGallery(gallery) {
            $location.path("/gallery/" + gallery);
        }

        function refreshGalleries(start) {
            start = start ? start : 0;

            services.load("galleries", {start: start}).success(function(data) {
                // TODO: are data correct?
                data = data.service;
                refreshListing(data.count, data.perPage);
                $scope.galleries = data.galleries;
            });
        }

        function refreshListing(totalCount, onPage) {
            $scope.listing = mg.utils.listing(totalCount, onPage);
        }
    }]);
