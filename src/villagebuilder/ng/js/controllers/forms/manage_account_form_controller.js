
app.controller('ManageAccountFormController', function($scope, $location, $http, Ajax, State, Request) {
    
    var form = 'account';
    var getUrl = Ajax.GET_ACCOUNT;
    var postUrl = Ajax.PUT_ACCOUNT;
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl, { user_id: State.userId });
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
                    Request.account.inputErrors = data;
                } else if (data.hasOwnProperty('errorMessage')) {  //resource not found (record missing)
                    Request.account.formError = data.errorMessage;
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


