app.controller('AccountInfoFormController', function($scope, $location, State, Request) {
    
$scope.request = {};
$scope.showInputErrors = false;

$scope.$watch(function() {return Request.createAccount}, function() {
    $scope.request = Request.createAccount;
});

$scope.validateForm = function(isValid) {
    if (isValid) {
        State.debug="validated";
        $location.path( "/create-account/map" );
    } else {
        State.debug="validate failed";
        $scope.showInputErrors = true;
    }
}

});

app.controller('AccountInfoFormFieldController1', function($scope) {
    $scope.inputFields = ['email','password','password_again','first_name','last_name'];
});


