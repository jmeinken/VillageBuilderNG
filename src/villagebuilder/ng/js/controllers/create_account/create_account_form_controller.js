
app.controller('CreateAccountFormController', function($scope, $location, $http, $state, Ajax, State, Request) {
    
    var form = 'createAccount';
    var getUrl = Ajax.GET_ACCOUNT;
    var postUrl = Ajax.POST_ACCOUNT;
    $scope.showInputErrors = false;
    $scope.showFormError = true;
    
    $scope.form = form;
   
    $scope.completion = [];
    $scope.completion['account_info'] = false;
    $scope.completion['address'] = false;
    $scope.completion['personal_info'] = false;
    
    $scope.accountInfoView = "create-account.account-info";
    $scope.mapView = "create-account.map";
    $scope.personalInfoView = "create-account.personal-info";
    
    $scope.$watch(function() {return State.uploadedImageData.complete}, function(value) {
        Request[form].request.pic_large = State.uploadedImageData.file;
        Request[form].request.pic_small = State.uploadedImageData.thumbFile;
        Request[form].request.pic_large_url = State.uploadedImageData.url;
        Request[form].request.pic_small_url = State.uploadedImageData.thumbUrl;
    });
    $scope.$watch(function() {return State.changedAddress}, function(value) {
        if (State.changedAddress) {
            $state.go($scope.personalInfoView);
        }
    });



    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }

    $scope.resetForm = function() {
        Request.reset(form);
    }

    $scope.validateAccountInfo = function() {
        State.debug="it worked!";
    }
    
    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            $scope.completion['personal_info'] = true;
            submitForm();
        } else {
            State.debug="validate failed";
            $scope.completion['personal_info'] = false;
            $scope.showInputErrors = true;
        }
    }
    
    function submitForm() {
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
                if (data.hasOwnProperty('inputErrors'))  {
                    Request.createAccount.inputErrors = data.inputErrors;
                    if ( data.inputErrors.hasOwnProperty('email') ||
                         data.inputErrors.hasOwnProperty('password') ||
                         data.inputErrors.hasOwnProperty('password_again') ||
                         data.inputErrors.hasOwnProperty('first_name') ||
                         data.inputErrors.hasOwnProperty('last_name') )
                    {
                        //$location.path( '/create-account/account-info' );
                        $state.go($scope.accountInfoView);
                    }
                }
                if (data.hasOwnProperty('errorMessage')) {
                    Request.createAccount.formError = data.errorMessage;
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


