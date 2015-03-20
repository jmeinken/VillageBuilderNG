
app.controller('CreateGuestFormController', function($scope, $location, $http, $state, Ajax, State, Request, ErrorHandler, Utilities) {
    
    var form = 'createGuest';
    var getUrl = Ajax.GET_GUEST;
    //var postUrl = Ajax.POST_GUEST;
    var postUrl = Ajax.POST_FRIENDSHIP_USING_EMAIL;
    
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
                ErrorHandler.formSubmission(data, status, 'createGuest');
                State.debug = data;
            });
    }




}); 





