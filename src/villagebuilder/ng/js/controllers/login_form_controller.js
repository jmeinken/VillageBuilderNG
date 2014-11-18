app.controller('LoginFormController', function($scope, $location, $http, Ajax, State, Request) {

    var form = 'login';
    var getUrl = Ajax.GET_LOG_IN;
    var postUrl = Ajax.POST_LOG_IN;
   
    $scope.testme = "testing";
   
    $scope.request = {};
    $scope.showInputErrors = false;

    $scope.$watch(function() {return Request.login}, function() {
        $scope.request = Request.login;
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



app.controller('LoginFormFieldController1', function($scope) {
    $scope.inputFields = ['email','password'];
});

