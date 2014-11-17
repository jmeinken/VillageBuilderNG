app.controller('TesterController', function($scope, State, Request, Ajax, $timeout) {
    
    $scope.submitted = false;
    
    $scope.myminn = '4';
    $scope.tester = 'nop';
    
    
    $scope.submitForm = function(isValid) {
	if (isValid) {
		State.debug="submitted";
	} else {
	  $scope.submitted = true;  //tells invalid fields to go ahead and show errors
          State.debug="submission failed";
	}
    };

    
});


