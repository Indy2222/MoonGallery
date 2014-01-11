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


moonGalleryControllers.controller('UploaderCtrl', ["$scope", "$http",
    function($scope, $http) {

        var CHUNK_SIZE = 1024 * 1024; // one MiB
        var lastUploadedChunk = 0;
        var lastUploadedFile = 0;
        var totalSize = 0;
        var uploaded = 0;
        var finishedCallback;
        var uploadingGalleryName;

        $scope.setFiles = function(element) {
            console.log('files:', element.files);
            // Turn the FileList object into an Array
            $scope.files = [];
            for (var i = 0; i < element.files.length; i++) {
                $scope.files.push(element.files[i]);
            }
        };

        $scope.uploadGallery = function() {
            if ($scope.uploading) {
                // cant upload two batches simultaneously
                return;
            }

            $scope.uploading = true;
            uploadingGalleryName = $scope.gallery.name;

            finishedCallback = function finished() {
                finishedCallback = null;

                $http.get("service.php?service=upload&create=" + encodeURIComponent(uploadingGalleryName))
                        .success(function(data) {
                            $scope.uploading = false;
                            $scope.progress = 100;
                            console.debug("Uploading finished!");
                        });
            };

            uploadFiles();
        };

        function uploadFiles() {
            $scope.gallery = {
                files: "",
                name: ""
            };

            totalSize = 0;
            uploaded = 0;
            lastUploadedChunk = 0;
            lastUploadedFile = 0;
            for (var i in $scope.files) {
                totalSize += $scope.files[i].size;
            }

            uploadNext();
        }

        function uploadNext() {
            var file = $scope.files[lastUploadedFile];
            var start = CHUNK_SIZE * lastUploadedChunk;

            if (start >= file.size) {
                file = null;
                lastUploadedFile++;
                lastUploadedChunk = 0;
                start = 0;

                if (lastUploadedFile < $scope.files.length) {
                    var file = $scope.files[lastUploadedFile];
                } else {
                    finishedCallback && finishedCallback();
                }
            }

            updateProgress();

            if (file != null) {
                var chunk = file.slice(start, start + CHUNK_SIZE);
                uploadChunk(chunk, file.name, start, file.size);
                lastUploadedChunk++;
            }
        }

        function uploadChunk(chunk, fileId, start, size) {
            var formData = new FormData();
            formData.append("file", chunk);

            var xhr = new XMLHttpRequest();

            xhr.addEventListener("load", uploadNext, false);
            xhr.addEventListener("error", uploadFailed, false);
            xhr.addEventListener("abort", uploadCanceled, false);
            xhr.open("POST", "service.php?service=upload&id="
                    + encodeURIComponent(fileId) + "&start=" + start + "&size=" + size);
            xhr.send(formData);
        }

        function updateProgress() {
            uploaded = 0;

            for (var i = 0; i < lastUploadedFile && i < $scope.files.length; i++) {
                uploaded += $scope.files[i].size;
            }
            uploaded += lastUploadedChunk * CHUNK_SIZE;

            // -10% because after upload gallery has to be created and written to database
            var progress = Math.round(1000 * (uploaded / totalSize - 0.1)) / 10;
            console.log("Uploading progress: " + progress);

            // update progress is not called from AngularJS
            $scope.$apply(function() {
                $scope.progress = progress;
            });
        }

        function uploadFailed(evt) {
            alert("There was an error attempting to upload a file.");
        }

        function uploadCanceled(evt) {
            alert("The upload has been canceled by the user or the browser dropped the connection.");
        }
    }]);
