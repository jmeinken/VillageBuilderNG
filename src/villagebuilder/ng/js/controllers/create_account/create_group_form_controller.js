
app.controller('CreateGroupFormController', function($scope, $location, $http, $state, Ajax, State, Request, ErrorHandler) {
    
    var form = 'createGroup';
    var getUrl = Ajax.GET_GROUP;
    var postUrl = Ajax.POST_GROUP;
    
    $scope.form = form;
   
    $scope.completion = [];
    $scope.completion['account_info'] = false;
    $scope.completion['address'] = false;
    $scope.completion['personal_info'] = false;

    $scope.accountInfoView = "main.create-group.account-info";
    $scope.mapView = "main.create-group.map";
    $scope.personalInfoView = "main.create-group.personal-info";
    
    $scope.$watch(function() {return State.uploadedImageData}, function(value) {
        Request[form].request.pic_large = State.uploadedImageData.file;
        Request[form].request.pic_small = State.uploadedImageData.thumbFile;
        Request[form].request.pic_large_url = State.uploadedImageData.url;
        Request[form].request.pic_small_url = State.uploadedImageData.thumbUrl;
    });
    $scope.$watch(function() {return State.changedAddress}, function(value) {
        if (State.changedAddress) {
            $state.go($scope.personalInfoView);
        }
    });

    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }

    $scope.resetForm = function() {
        Request.reset(form);
    }

    //$scope.validateAccountInfo = function() {
    //    State.debug="it worked!";
    //}
    
    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            $scope.completion['personal_info'] = true;
            submitForm();
        } else {
            State.debug="validate failed";
            $scope.completion['personal_info'] = false;
            $scope.showInputErrors = true;
        }
    }
    
    function submitForm() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.authenticate();
                State.infoTitle = "Group Created";
                State.infoMessage = "Your group was successfully created.";
                State.infoLinks = [
                        {"link" : "#/home", "description": "Return to Home Page"}
                    ];
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.createGroupFormSubmission(data, status, 'createGroup');
            });
    }
    

    
    
    



    
    
    

   
 



}); 


