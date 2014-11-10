app.controller('LoginFormController', function($scope, $location, $http, Ajax, State, Request) {

   var form = 'login';
   var getUrl = Ajax.GET_LOG_IN;
   var postUrl = Ajax.POST_LOG_IN;
   
   $scope.loadForm = function() {
       Request.loadForm(form, getUrl);
   }
    
    $scope.resetForm = function() {
        Request.reset(form);
    }

    //log user in
    $scope.submitForm = function() {
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


