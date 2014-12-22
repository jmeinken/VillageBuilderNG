
app.controller('ResetPasswordFormController', function($scope, $location, $http, Ajax, State, Request, Utilities, ErrorHandler) {
    
    var form = 'resetPassword';
    var getUrl = Ajax.GET_RESET_PASSWORD;
    var postUrl = Ajax.POST_RESET_PASSWORD;
   
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;
    
    $scope.$watch(function() {return Request[form]}, function() {
        $scope.request = Request[form];
        $scope.inputFields = Utilities.keyArray(Request[form].request);
    });
   
    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }
    
    $scope.resetForm = function() {
        Request.reset(form);
    }
    
    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            submitForm();
        } else {
            State.debug="validate failed";
            $scope.showInputErrors = true;
        }
    }
   
    function submitForm() {
        Request[form].inputErrors = {};
        Request[form].formError = "";
        $http.post(postUrl, Request[form].request).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.formSubmission(data, status, 'resetPassword');
            });
    }
    
  


}); 


