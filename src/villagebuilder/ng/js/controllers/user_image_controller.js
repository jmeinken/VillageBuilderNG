
app.controller('UserImageController', function($scope, State, Request, Ajax, $timeout) {
    
    //$scope.userImage;
    $scope.imageLoaded = 0;         //needs to be set using jquery and hidden input
    $scope.imageUploaded = false;
    $scope.imgLarge = "assets/images/generic-user.png";
    
    $scope.api;  //used by jCrop and Upload
    $scope.options = {
        fileInputs: ['#file'],
        canvases: {
            large: {
                'object': '#img_large'
            },
            thumb: {
                'object': '#img_thumb'
            },
            viewer: {
                'object': '#img_viewer'
            }},
        dragDrops: ['#dragDrop'],
        afterPick: function () {
            this.setSelect([0, 0, 5300, 5300])  // initial selection
        },
        beforePick: function (img) {
            var imageLoaded = $('#imageLoaded');
            imageLoaded.val('1');
            imageLoaded.trigger('input');
            //$('#imageLoaded').val(1);
            //$('#imageLoaded').triggerHandler("change");
            if (img) {//valid file
                if (img.width < 300 || img.height < 300) {
                    alert('The image width and height must be over or equal to 300!')
                    return true;  // ignore this img
                }
            }
            else {// invalid file
                alert('Invalid file!')
            }
            
        },
        aspectRatio: 1 / 1,
        minSize: [200, 150],
        boxWidth: 400,
        boxHeight: 400
    }
    
    

    
    $scope.attachJcrop = function() {
        $('#image').Jcrop($scope.options, function(){$scope.api = this});
    }
    
    $scope.uploadImage = function() {
        if (!$scope.api) {
            return;
        }
        if (!$scope.api.isSelected()) {
            alert('No selection!')
            return;
        }
        var $progressBar = $('#progressBar')
        function progress(percent, $element) {
            var progressBarWidth = percent * $element.width();
            $element.find('div').animate({ width: progressBarWidth }, 50).html(Math.round(percent * 100) + "%&nbsp;");
        }
        $progressBar.find('div').width(0)
        //sources
        var sources = [
            {
                key: 'large' // key in options.canvases
                //format: 'image/png' // default = 'image/jpeg'
            },
            {
                key: 'thumb' // key in options.canvases
                //field: 'small'  // default = 'file'
            },
        ];
        //settings
        var settings = {
            url: Ajax.POST_USER_IMAGE,
            data: {}, //three fields (medium, small, text) to upload
            onprogress: function (evt) { // function for xhr.upload.onprogress
                if (evt.lengthComputable) {
                    progress(evt.loaded / evt.total, $progressBar)
                }
            },
            success: function (data) {
                $timeout( function() {
                    State.debug = data;
                    $scope.imageUploaded = true;
                    $scope.imgLarge = "../public/assets/images/" + data.largeFileName;
                }, 1000);
            }
        };
        $scope.api.upload(sources, settings);
    }
 


}); 


