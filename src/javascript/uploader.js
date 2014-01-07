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


moonGalleryControllers.controller('UploaderCtrl', function($scope) {

    var CHUNK_SIZE = 1024 *1024; // one MiB
    var lastUploadedChunk = 0;
    var lastUploadedFile = 0;
    var totalSize = 0;
    var uploaded = 0;
    var finishedCallback;

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

        finishedCallback = function finished() {
            finishedCallback = null;

            var xhr = new XMLHttpRequest();

            xhr.addEventListener("load", function() {
                $scope.uploading = false;

                // TODO: empty form
                //$scope.gallery = {
                //    files: "",
                //    name: ""
                //};

                console.debug("Uploading finished!");
            }, false);

            xhr.open("POST", "service.php?service=upload&create=" + encodeURIComponent($scope.gallery.name));
            xhr.send(null);
        };

        uploadFiles();
    };

    function uploadFiles() {
        totalSize = 0;
        uploaded = 0;
        lastUploadedChunk = 0;
        lastUploadedFile = 0;
        for (var i in $scope.files) {
            totalSize += $scope.files[i].size;
        }

        uploadNext();
    };

    function uploadNext() {
        updateProgress();

        var file = $scope.files[lastUploadedFile];
        var start = CHUNK_SIZE * lastUploadedChunk;

        if (start >= file.size) {
            file = null;
            lastUploadedFile++;

            if (lastUploadedFile < $scope.files.length) {
                var file = $scope.files[lastUploadedFile];
                lastUploadedChunk = 0;
                start = CHUNK_SIZE * lastUploadedChunk;
            } else {
                finishedCallback && finishedCallback();
            }
        }

        if (file != null) {
            var chunk = file.slice(start, CHUNK_SIZE);
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

        for (var i = 0; i < lastUploadedFile; i++) {
            uploaded += $scope.files[i].size;
        }
        uploaded += lastUploadedChunk * CHUNK_SIZE;

        $scope.progress = Math.round(1000 * uploaded / totalSize) / 100;
    }

    function uploadFailed(evt) {
        alert("There was an error attempting to upload the file.")
    }

    function uploadCanceled(evt) {
        alert("The upload has been canceled by the user or the browser dropped the connection.")
    }
});
