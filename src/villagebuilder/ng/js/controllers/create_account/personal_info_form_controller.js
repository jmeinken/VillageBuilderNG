app.controller('PersonalInfoFormController', function($scope, $location, $http, Ajax, State, Request) {
    
    var form = 'createAccount';
    var getUrl = Ajax.GET_ACCOUNT;
    var postUrl = Ajax.POST_ACCOUNT;
    
    $scope.request = {};
    $scope.showInputErrors = false;

    $scope.$watch(function() {return Request.createAccount}, function() {
        $scope.request = Request.createAccount;
    });

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
                State.debug = data;
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

app.controller('PersonalInfoFormFieldController1', function($scope) {
    $scope.inputFields = ['street','city','neighborhood'];
});

app.controller('PersonalInfoFormFieldController2', function($scope) {
    $scope.inputFields = ['share_email','share_address','share_phone'];
});

app.controller('PersonalInfoFormFieldController3', function($scope) {
    $scope.inputFields = ['phone_number','phone_type'];
});

