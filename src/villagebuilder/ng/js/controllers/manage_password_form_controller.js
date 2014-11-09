
app.controller('ManagePasswordFormController', function($scope, $location, $http, Ajax, State, Request) {
    
    var form = 'password';
    var getUrl = Ajax.GET_PASSWORD;
    var postUrl = Ajax.PUT_PASSWORD;
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }
    
    $scope.resetForm = function() {
        Request.reset(form);
    }
        

   
    $scope.submitForm = function() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (status == 400)  {  //bad request (validation failed)
                    Request.password.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.password.formError = data.errorMessage;
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


