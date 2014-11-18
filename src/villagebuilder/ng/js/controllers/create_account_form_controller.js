
app.controller('CreateAccountFormController', function($scope, $location, $http, Ajax, State, Request) {
    
   var form = 'createAccount';
   var getUrl = Ajax.GET_ACCOUNT;
   var postUrl = Ajax.POST_ACCOUNT;
   

   
   $scope.loadForm = function() {
       Request.loadForm(form, getUrl);
   }
    
    $scope.resetForm = function() {
        Request.reset(form);
    }
    
    $scope.validateAccountInfo = function() {
        State.debug="it worked!";
    }
    

    
    
    $scope.submitForm = function() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                State.debug = status;
                State.infoTitle = "Almost There";
                State.infoMessage = "Check your email to confirm account creation.";
                State.infoLinks = [
                        {"link" : "#/login", "description": "Return to Login Page"}
                    ];
                $location.path( '/info' );
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.createAccount.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.createAccount.formError = data.errorMessage;
                } else {  // token mismatch (500), unauthorized (401), etc.
                    State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                    State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
                    State.infoLinks = [
                        {"link" : "#/login", "description": "Return to Login Page"}
                    ];
                    $location.path( '/info' );
                }
            });
    }



    
    
    

   
 



}); 


