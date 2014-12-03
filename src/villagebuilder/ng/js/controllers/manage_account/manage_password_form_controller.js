
app.controller('ManagePasswordFormController', function($scope, $location, $http, Ajax, State, Request, Utilities) {
    
    var form = 'password';
    var getUrl = Ajax.GET_PASSWORD;
    var postUrl = Ajax.PUT_PASSWORD;
   
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;

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
        Request.loadForm(form, getUrl, { user_id: State.activeAccount.userId });
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
                State.debug = status;
                if (data.hasOwnProperty('inputErrors'))  {
                    Request.password.inputErrors = data.inputErrors;
                }
                if (data.hasOwnProperty('errorMessage')) {
                    Request.password.formError = data.errorMessage;
                }
                if (!data.hasOwnProperty('errorMessage') && !data.hasOwnProperty('inputErrors')) {
                    State.debug = data;
                    State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                    State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
                    State.infoLinks = [
                        {"link" : "#/home", "description": "Return to Home Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }
    
  


}); 


