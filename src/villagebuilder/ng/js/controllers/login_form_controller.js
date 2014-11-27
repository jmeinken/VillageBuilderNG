app.controller('LoginFormController', function($scope, $location, $http, Ajax, State, Request, Utilities) {

    var form = 'login';
    var getUrl = Ajax.GET_LOG_IN;
    var postUrl = Ajax.POST_LOG_IN;
   
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
    
    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            submitForm();
        } else {
            State.debug="validate failed";
            $scope.showInputErrors = true;
        }
    }

    function submitForm() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.userId = data.user_id;
                State.authenticated = true;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.login.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.login.formError = data.errorMessage;
                    State.debug="error message found";
                } else {  // token mismatch (500), unauthorized (401), etc.
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

