app.controller('PersonalInfoFormController', function($scope, $location, $http, $state, Ajax, State, Request) {
    

    
    $scope.request = {};

    $scope.$watch(function() {return Request[$scope.$parent.form]}, function() {
        $scope.request = Request[$scope.$parent.form];
    });
    
    //$scope.$watch(function() {return State.currentUser.profilePicFile}, function(value) {
    //   Request[form].request.pic_large = State.currentUser.profilePicFile;
    //});
    
    //$scope.$watch(function() {return State.currentUser.profilePicThumbFile}, function(value) {
     //   Request[form].request.pic_small = State.currentUser.profilePicThumbFile;
    //});
    
    

    
    
    

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

