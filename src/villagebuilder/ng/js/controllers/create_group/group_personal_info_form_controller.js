app.controller('GroupPersonalInfoFormController', function($scope, $location, $http, $state, Ajax, State, Request) {
    

    
    $scope.request = {};
    $scope.showInputErrors = false;

    $scope.$watch(function() {return Request.createAccount}, function() {
        $scope.request = Request.createAccount;
    });
    


    
    
    

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

