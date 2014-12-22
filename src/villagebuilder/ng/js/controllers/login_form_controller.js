app.controller('LoginFormController', function($scope, $location, $http, Ajax, State, Request, ErrorHandler, Utilities) {

    var form = 'login';
    var getUrl = Ajax.GET_LOG_IN;
    var postUrl = Ajax.POST_LOG_IN;
   
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;
    $scope.showFormError = true;

    $scope.$watch(function() {return Request[form]}, function() {
        $scope.request = Request[form];
        $scope.inputFields = Utilities.keyArray(Request[form].request);
    });

    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
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
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.formSubmission(data, status, 'login');
            });
    }


}); 

