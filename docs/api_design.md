
***API RULES***

GENERAL ERROR HANDLING

status exceptions that provide information for the user are 400, 403, 404
all other exceptions can be handled, but they won't offer information designed to show to user
in general, errors might provide 
    'errorMessage' => "Error description" as a general message and specific information about
    'inputErrors' => [array] as a collection of errors associated with specific fields

---------------------

SINGLE RECORD GET QUERY
/api/get-[myTable] ()
/api/get-[myTable] ([recordId])

get queries for single record generally return 
$values (the request object as a set of key-value pairs)
$meta (organized same as request object)

get queries for single record can pass nothing (returns default values) or record ID (returns record values)

errors:
	404 NOT FOUND (recordId not found in database)
    errorMessage => '[error description]'
	
----------------

MULTI-RECORD GET QUERY
/api/get-collection-[myTable] (startPos, endPos)

$values[0]
$values[1]
etc.
$meta

if startPos not provided, returns records 1 to endPos
if endPos not provided, returns from start position to end of recordset
if neither is provided, returns entire recordset
if endPos is beyond last record, returns up to last record

errors:
	404 NOT FOUND (start position is after last record in set)
    errorMessage => '[error description]'
	
----------------

POST QUERY
/api/post-[myTable] ($values from GET request)

success:
    recordId => '[record ID]'

errors:
	400 BAD REQUEST	(one of the inputs failed to validate)
	'inputErrors' -> [field] -> ['[error description]', '[error description]']
	
	403 FORBIDDEN (you don't have permission to post that record - because record already exists, etc.)
	['errorMessage' => '[error description]']
	[field] -> ['[error description]', '[error description]']
	
----------------

PUT QUERY
/api/put-[myTable] ($values from GET request)

success:
    recordId => '[record ID]'

errors:
	400 BAD REQUEST	
	'inputErrors' -> [field] -> ['[error description]', '[error description]']
	
	403 FORBIDDEN (you don't have permission to make provided modifications)
	'errorMessage' => '[error description]'
	'inputErrors' -> [field] -> ['[error description]', '[error description]']
	
	404 NOT FOUND (record doesn't exist)
	'errorMessage' => '[error description]'
	
----------------

DELETE QUERY
/api/delete-[myTable] (recordId)

success:
    recordId => '[record ID]'

errors:
	400 BAD REQUEST	(record not provided)
	
	403 FORBIDDEN (you don't have permission to remove record)
	['errorMessage' => '[error description]']
	
	404 NOT FOUND (record doesn't exist)
	'errorMessage' => '[error description]'
	
----------------

***METADATA FOR AUTOMATED FORM GENERATION AND VALIDATION***

All fields:
type = string, float, integer, boolean
input_type => text, number, integer, password, password_confirm, select, checkbox
name
description
*note: most validation fields have a corresponding optional error message that can be provided (ex. 'required' and 'required_error')

TEXT FIELDS
required
minlength
maxlength
pattern = '/mypattern/'
pattern_error

EMAIL FIELDS
(validation doesn't seem to work right.  Use text with RE pattern instead.)
    
NUMBER FIELDS
required
min
max

INTEGER FIELDS
required
min
max

PASSWORD
required
matches
minlength
maxlength
pattern

PASSWORD CONFIRM
required
matches = [field to match]

SELECT
required
options = [key:value array where value is display value]




***API DETAILED***

/api/check-login-status () *non-standard

	200
	$response['logged_in'] = true;
	$response['user_type'] = 'member';
	$response['userId']
	$response['email']
	$response['firstName']
	$response['lastName']
	$response['profilePicFile']
	$response['profilePicUrl']
	$response['profilePicThumbFile']
	$response['profilePicThumbUrl']

	200
	$response['logged_in'] = false;

/api/get-log-in ()

	200
	$values['email']
	$values['password']
	$values['remember']
	$values['_token']
	$meta
	
/api/post-log-in (get-log-in values)

	200
	$response['userId']
				
	400 BAD REQUEST	
	'inputErrors => validation array
	
	404 NOT FOUND
	'errorMessage' => 'Unable to log in.  Check your email and password.'
	
/api/post-log-out () *non-standard

	200
	'message' => 'logged out'
	
/api/post-activate-account (60 character activation code) *non-standard

	200
	['message' => "account activated"]

	400 BAD REQUEST
	['errorMessage' => "There was a problem with the account activation code. Did you copy the entire link?"]
	
	404 NOT FOUND
	['errorMessage' => "Unable to activate account.  Has this account already been activated?"]
	
	500 Internal Server Error
	'error' => "unknown error"

/api/post-activate-reset-password (60 character activation code) *non-standard

	200
	['message' => "password reset"]

	400 BAD REQUEST
	['errorMessage' => "There was a problem with the password reset activation code. Did you copy the entire link?"]
	
	404 NOT FOUND
	['errorMessage' => "Unable to complete resetting password.  Has this password already been reset?"]
	
	500 Internal Server Error
	'error' => "unknown error"
	

/api/get-reset-password () *non-standard

	200
	$values['email']
    $values['_token']
	$meta

/api/post-reset-password (get-reset-password $values) *non-standard

	200
	['message' => 'success']

	400 BAD REQUEST	
	'inputErrors' => validation array
	
	404 NOT FOUND
	['errorMessage' => 'Failed to reset password.  Did you enter the correct email address?']
	
/api/get-password ()

	200
	$values['old_password']
	$values['new_password']
	$values['new_password_again']
	$values['_token']
	$meta
	
/api/put-password (get-password $values)

	200
	['message' => 'success']

	400 BAD REQUEST	
	'inputErrors' => validation array
	
	404 NOT FOUND
	['errorMessage' => 'old password incorrect']
	
	404 NOT FOUND
	['errorMessage' => 'failed']
	
/api/post-user-image ('thumb' file, 'large' file)
	
	200
	'pic_small' => [
		'name' => $thumbFileName,
		'path' => Config::get('constants.profilePicUrlPath') . $thumbFileName
	],
	'pic_large' => [
		'name' => $largeFileName,
		'path' => Config::get('constants.profilePicUrlPath') . $largeFileName
	]
	
	400 BAD REQUEST
	['errorMessage' => 'Photo not sent.  Please try again.']

/api/get-account ()

	200
	$values['email']
	$values['password']
	$values['password_again']
	$values['first_name']
	$values['last_name']
	$values['address1']
	$values['address2']
	$values['city']
	$values['state']
	$values['zip_code']
	$values['full_address']
	$values['latitude']
	$values['longitude']
	$values['street']
	$values['neighborhood']
	$values['phone_number']
	$values['phone_type']
	$values['share_email']
	$values['share_address']
	$values['share_phone']
	$values['pic_large']
	$values['pic_small']
	$values['_token']
	$meta
	
/api/get-account ('user_id')

	200
	same as above except these included:
		$values['user_id']
		$values['member_id']
	and these excluded:
		$values['password']
		$values['password_again']
	$meta
	
	[should add error handling if user_id isn't found]

/api/post-account (get-account $values)

	200
	['user' => Input::get('email')]

	400 BAD REQUEST	
	'inputErrors' => validation array

	403 FORBIDDEN
	'inputErrors' => ['email' => ['Email already in use.  Do you already have an account?']]
	
	500
	'query failed'

/api/put-account (get-account(user_id) $values)

	200
	['user' => Input::get('email')]
	
	400 BAD REQUEST	
	'inputErrors' => validation array
	
	400 BAD REQUEST
	['message' => 'user not found']
	
	404 NOT FOUND
	Laravel exception












