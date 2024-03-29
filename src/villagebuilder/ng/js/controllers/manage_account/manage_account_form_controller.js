
app.controller('ManageAccountFormController', function($scope, $location, $timeout, $http, Ajax, State, Request, ErrorHandler) {
    
    var form = 'account';
    if (State.activeParticipant.participant_type == 'person') {
        var getUrl = Ajax.GET_ACCOUNT;
        var postUrl = Ajax.PUT_ACCOUNT;
        var deleteUrl = Ajax.DELETE_ACCOUNT;
    } else {
        var getUrl = Ajax.GET_GROUP;
        var postUrl = Ajax.PUT_GROUP;
        var deleteUrl = Ajax.DELETE_GROUP;
    }
    $scope.showImputErrors = true;
    $scope.showFormError = true;
    
    $scope.$watch(function() {return Request[form]}, function() {
        $scope.request = Request[form];
        $scope.inputFields = Utilities.keyArray(Request[form].request);
    });
    
    //very important: State.uploadedImageData and State.changed address
    //MUST be reset to {} and false respectively after being used.
    //Otherwise, submitForm() will be triggered when it shouldn't
    $scope.$watch(function() {return State.uploadedImageData}, function(value) {
        if (State.uploadedImageData.hasOwnProperty('file')) {
            Request[form].request.pic_large = State.uploadedImageData.file;
            Request[form].request.pic_small = State.uploadedImageData.thumbFile;
            Request[form].request.pic_large_url = State.uploadedImageData.url;
            Request[form].request.pic_small_url = State.uploadedImageData.thumbUrl;
            submitForm();
        }
    });
    $scope.$watch(function() {return State.changedAddress}, function(value) {
        if (State.changedAddress) {
            submitForm();
        }
    });
    
    $scope.$watch(function() {return State.activeParticipant}, function(value) {
        if (State.activeParticipant.participant_type == 'person') {
            getUrl = Ajax.GET_ACCOUNT;
            postUrl = Ajax.PUT_ACCOUNT;
            deleteUrl = Ajax.DELETE_ACCOUNT;
        } else {
            getUrl = Ajax.GET_GROUP;
            postUrl = Ajax.PUT_GROUP;
            deleteUrl = Ajax.DELETE_GROUP;
        }
        Request.loadForm(form, getUrl, { 
            participant_id: State.activeParticipant.participant_id, 
            user_id: State.activeParticipant.user_id
        });
    });
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl, { 
            participant_id: State.activeParticipant.participant_id,
            user_id: State.activeParticipant.user_id
        });
    }
    
    $scope.cancelSubmit = function() {
        Request.loadForm(form, getUrl, { participant_id: State.activeParticipant.participant_id });
        for (field in State.accountDataEditToggle) {
            State.accountDataEditToggle[field] = false;
        }
    }
    
    $scope.deleteAccount = function() {
        $http.post(deleteUrl, {
            'user_id': State.activeParticipant.user_id,
            'participant_id': State.activeParticipant.participant_id
        }).
            success(function(data, status, headers, config) {
                alert("Account successfully deleted");
                if (State.activeParticipant.participant_type=='person') {
                    State.signOut();
                } else {
                    for (participant in State.allParticipants) {
                        if (State.allParticipants[participant].participant_type == "person") {
                            State.activeParticipant = State.allParticipants[participant];
                        }
                    }
                    State.authenticate();
                    $location.path( '/main/home' );
                }
            }).
            error(function(data, status, headers, config) {
                alert('There was a problem deleting your account.');
                State.debug = data;
            });
    }
    
    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            submitForm();
        } else {
            State.debug="validate failed";
            $scope.showInputErrors = true;
        }
    } 
   
    submitForm = function() {
        //update request with any images that have been uploaded
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                for (field in State.accountDataEditToggle) {
                    State.accountDataEditToggle[field] = false;
                }
                State.uploadedImageData = {};
                //State.changedAddress = false;
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.formSubmission(data, status, 'account');
            });
    }

}); 

app.controller('ManageAccountInputEmailController', function($scope) {
    $scope.field = 'email';
});
app.controller('ManageAccountInputNameController', function($scope) {
    $scope.inputFields = ['first_name', 'last_name'];
});
app.controller('ManageAccountInputTitleController', function($scope) {
    $scope.inputFields = ['title', 'description'];
});
app.controller('ManageAccountInputPrivacyController', function($scope) {
    $scope.inputFields = ['share_email', 'share_address', 'share_phone'];
});
app.controller('ManageAccountInputPhoneController', function($scope) {
    $scope.inputFields = ['phone_number', 'phone_type'];
});
app.controller('ManageAccountInputAddressController', function($scope) {
    $scope.inputFields = ['street', 'city', 'neighborhood'];
});



