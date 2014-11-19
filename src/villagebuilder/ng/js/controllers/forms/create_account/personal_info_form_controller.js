app.controller('PersonalInfoFormController', function($scope, $location, State, Request) {
    
    $scope.request = {};
    $scope.showInputErrors = false;

    $scope.$watch(function() {return Request.createAccount}, function() {
        $scope.request = Request.createAccount;
    });

    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
        } else {
            State.debug="validate failed";
            $scope.showInputErrors = true;
        }
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

