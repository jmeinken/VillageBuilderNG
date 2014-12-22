
app.service("ErrorHandler", function(Request, State, Ajax, $location, $state) {
    
// Right now, this deals with complex Ajax error handling.
// It does not handle EVERY Ajax error and it does not handle other types of errors.
// I might expand it's role on the site in the future.
    
 
    this.formSubmission = function(data, status, formName) {
        State.debug = status;
        if (data.hasOwnProperty('inputErrors'))  {
            Request[formName].inputErrors = data.inputErrors;
        } else if (data.hasOwnProperty('errorMessage')) {
            Request[formName].formError = data.errorMessage;
        }
        else if (data == "Unauthorized") {
            State.authenticate();
        } else {
            State.debug = data;
            State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
            State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
            State.infoLinks = [
                {"link" : "#/home", "description": "Return to Home Page"}
            ];
            $location.path( '/info' );
        }
    }
    
    this.createAccountFormSubmission = function(data, status, formName) {
        State.debug = status;
        if (data.hasOwnProperty('inputErrors'))  {
            Request[formName].inputErrors = data.inputErrors;
            if ( data.inputErrors.hasOwnProperty('email') ||
                 data.inputErrors.hasOwnProperty('password') ||
                 data.inputErrors.hasOwnProperty('password_again') ||
                 data.inputErrors.hasOwnProperty('first_name') ||
                 data.inputErrors.hasOwnProperty('last_name') )
            {
                //$location.path( '/create-account/account-info' );
                $state.go('create-account.account-info');
            }
        } else if (data.hasOwnProperty('errorMessage')) {
            Request[formName].formError = data.errorMessage;
        } else {
            State.debug = data;
            State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
            State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
            State.infoLinks = [
                {"link" : "#/home", "description": "Return to Home Page"}
            ];
            $location.path( '/info' );
        }
    }
    
    this.createGroupFormSubmission = function(data, status, formName) {
        State.debug = status;
        if (data.hasOwnProperty('inputErrors'))  {
            Request[formName].inputErrors = data.inputErrors;
            if ( data.inputErrors.hasOwnProperty('email') ||
                 data.inputErrors.hasOwnProperty('title') ||
                 data.inputErrors.hasOwnProperty('description') )
            {
                //$location.path( '/create-account/account-info' );
                $state.go('main.create-group.account-info');
            }
        }
        if (data.hasOwnProperty('errorMessage')) {
            Request[formName].formError = data.errorMessage;
        }
        if (!data.hasOwnProperty('errorMessage') && !data.hasOwnProperty('inputErrors')) {
            State.debug = data;
            State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
            State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
            State.infoLinks = [
                {"link" : "#/home", "description": "Return to Home Page"}
            ];
            $location.path( '/info' );
        }
    }
    
    this.activationCodeError = function(data, status) {
        State.debug = status;
            if (data.hasOwnProperty('errorMessage')) {
                State.infoTitle = "Error";
                State.infoMessage = data.errorMessage;
            } else {
                State.infoTitle = Ajax.ERROR_GENERAL_TITLE;
                State.infoMessage = Ajax.ERROR_GENERAL_DESCRIPTION;
            }
            State.infoLinks = [
                {"link" : "#/login", "description": "Go to Login Page"}
            ];
            $location.path( '/info' );
    }
 
 
 

});