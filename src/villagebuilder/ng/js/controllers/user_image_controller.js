
app.controller('UserImageController', function($scope, State, Request, fileReader, $timeout) {
    
    $scope.userImage;
    
    $scope.api;  //used by jCrop and Upload
    $scope.options = {
        fileInputs: ['#file'],
        canvases: {
            medium: {
                'object': '#medium'
            },
            small: {
                'object': '#small'
            }},
        dragDrops: ['#dragDrop'],
        afterPick: function () {
            this.setSelect([0, 0, 400, 300])  // initial selection
        },
        beforePick: function (img) {
            if (img) {//valid file
                if (img.width < 300 || img.height < 300) {
                    alert('The image width and height must be over or equal to 300!')
                    return true  // ignore this img
                }
            }
            else {// invalid file
                alert('Invalid file!')
            }
        },
        aspectRatio: 4 / 3,
        minSize: [200, 150],
        boxWidth: 400,
        boxHeight: 400
    }
    
    
    //console.log(fileReader)
    $scope.getFile = function () {
        //$scope.progress = 0;
        fileReader.readAsDataUrl($scope.file, $scope)
                      .then(function(result) {
                          $scope.imageSrc = result;
                          $timeout( function() {
                              $('#user_image').Jcrop();                              
                          }, 1000);
                              
                      });
    };
    
    $scope.attachJcrop = function() {
        $('#image').Jcrop($scope.options, function(){$scope.api = this});
    }
    
    $scope.uploadImage = function() {
        if($scope.api && $scope.api.isSelected()){
            $scope.api.upload(sources, settings);
        }
    }
 
    /*
    $scope.$on("fileProgress", function(e, progress) {
        $scope.progress = progress.loaded / progress.total;
    });
    */

}); 


