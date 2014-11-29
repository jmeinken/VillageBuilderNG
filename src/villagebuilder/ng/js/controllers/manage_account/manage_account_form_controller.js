
app.controller('ManageAccountFormController', function($scope, $location, $http, Ajax, State, Request) {
    
    var form = 'account';
    var getUrl = Ajax.GET_ACCOUNT;
    var postUrl = Ajax.PUT_ACCOUNT;
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl, { user_id: State.currentUser.userId });
    }
    
    $scope.cancelSubmit = function() {
        Request.loadForm(form, getUrl, { user_id: State.currentUser.userId });
        for (field in State.accountDataEditToggle) {
            State.accountDataEditToggle[field] = false;
        }
    }
    
    /*
    $scope.$watch(function() {return State.currentUser.profilePicFile}, function(value) {
        Request[form].request.pic_large = State.currentUser.profilePicFile;
        //submitForm();
    });
    
    $scope.$watch(function() {return State.currentUser.profilePicThumbFile}, function(value) {
        Request[form].request.pic_small = State.currentUser.profilePicThumbFile;
        //submitForm();
    });
    */
    
    

   
    $scope.submitForm = function() {
        //update request with any images that have been uploaded
        Request[form].request.pic_large = State.currentUser.profilePicFile;
        Request[form].request.pic_small = State.currentUser.profilePicThumbFile;
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                for (field in State.accountDataEditToggle) {
                    State.accountDataEditToggle[field] = false;
                }
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = status;
                if (data.hasOwnProperty('inputErrors'))  {
                    Request.account.inputErrors = data.inputErrors;
                }
                if (data.hasOwnProperty('errorMessage')) {
                    Request.account.formError = data.errorMessage;
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


