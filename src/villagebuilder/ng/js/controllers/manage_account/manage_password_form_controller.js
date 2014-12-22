
app.controller('ManagePasswordFormController', function($scope, $location, $http, Ajax, State, Request, Utilities, ErrorHandler) {
    
    var form = 'password';
    var getUrl = Ajax.GET_PASSWORD;
    var postUrl = Ajax.PUT_PASSWORD;
   
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;
    $scope.showFormError = true;

    $scope.$watch(function() {return Request[form]}, function() {
        $scope.request = Request[form];
        $scope.inputFields = Utilities.keyArray(Request[form].request);
    });
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }
    
    $scope.resetForm = function() {
        Request.reset(form);
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
    
    $scope.cancelSubmitPassword = function() {
        Request.loadForm(form, getUrl, { user_id: State.activeParticipant.userId });
        for (field in State.accountDataEditToggle) {
            State.accountDataEditToggle[field] = false;
        }
    }
        

   
    function submitForm() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                alert('Password successfully changed.');
                State.accountDataEditToggle['password'] = false;
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.formSubmission(data, status, 'password');
            });
    }
    
  


}); 


