
app.controller('CreateGuestFormController', function($scope, $location, $http, $state, Ajax, State, Request, ErrorHandler, Utilities) {
    
    var form = 'createGuest';
    var form2 = 'createGuestVerify';
    var getUrl = Ajax.GET_ADD_RELATIONSHIP_BY_EMAIL;
    //var postUrl = Ajax.POST_GUEST;
    var getUrl2 = Ajax.GET_ADD_RELATIONSHIP_BY_EMAIL_VERIFY;
    var postUrl = Ajax.POST_ADD_RELATIONSHIP_BY_EMAIL;
    var counter = 0;
    
    

    
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;
    $scope.showFormError = true;
    

    $scope.$watch(function() {return Request[form]}, function() {
        $scope.requests = Request[form];
        $scope.inputFields = Utilities.keyArray(Request[form][0].request);
    });

    /*
     * loadArrayForm lets us use multiple copies of the form at once
     */
    $scope.loadForm = function() {
        Request.loadArrayForm(form, getUrl, {}, counter);
        counter++;
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
    
    $scope.validateForm2 = function() {
            submitVerification();
    }
    
    function submitForm() {
        parameters = { 'request' : JSON.stringify(Request[form]) };
        $http.get(getUrl2, {params: parameters}).
            success(function(data, status, headers, config) {
                //State.debug = data[0];
                Request[form2] = data[0];
                $state.go('main.add-friends');
            }).
            error(function(data, status, headers, config) {
                ErrorHandler.formSubmission(data, status, 'createGuest');
                State.debug = data;
            });
    }
    
    function submitVerification() {
        $http.post(postUrl, { 'request' : JSON.stringify(Request[form2]) }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                //ErrorHandler.createAccountFormSubmission(data,status,'createAccount');
                State.debug = data;
            });
    }




}); 





