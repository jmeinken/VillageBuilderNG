<?php


/**
 * An account corresponds to a single set of login credentials on the site.
 * For historic reasons, the table for account info is called 'Users'.
 * 
 * The definition of Account is a little confused.  Some of these methods are
 * for USER accounts, others are specifically for PERSON accounts.
 * 
 */
class ApiAccount extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   /**
    * Creates an account for a PERSON.  Account is initialized as inactive, and
    * an email is sent to activate.
    * 
    * Returns errors if form data doesn't validate, email is already associated 
    * with an account or attempt to create account fails.
    * 
    * @return type
    */
    public function postAccount() {
        //validate input
        $validator = Validator::make( Input::all(), $this->postAccountValidator() );
        if ($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        }
        //return error if account already exists
        if ( AccountModel::accountExists('email', Input::get('email')) ) {
            $inputErrors = ['email' => ['Email already in use.  Do you already have an account?']];
            return Response::json(['inputErrors' => $inputErrors], self::STATUS_FORBIDDEN);
        }
        //insert record for user, member and person
        $code = str_random(60);
        $success = AccountModel::createAccount($code);
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        //send account activation email
        $email = Input::get('email');
        $name = Input::get('first_name');
        Mail::send('emails.auth.activate', array(
                'link' =>  'http://johnmeinken.com/vb-dev/src/villagebuilder/ng/#/activate-account/' . $code,
                'username' => $name                
            ), function($message) use ($email, $name) {
                $message->to($email, $name)->subject('Activate account');
            }
        );
        //return success response
        return Response::json(['user' => Input::get('email')], self::STATUS_OK);
    }
    

    /**
     * Accepts two image files ('thumb' and 'large').  Saves these files to 
     * disk and returns the file path to the files.
     * 
     * @return json string
     */
    public function postUserImage() {
        //return Response::json(['message' => $ct], self::STATUS_OK);
        if (Input::hasFile('thumb') && Input::hasFile('large')) {
            $ct = $this->getNextNumber(Config::get('constants.profilePicFilePath') . 'count.txt');
            $thumbFileName = 'user_thumb' . $ct . '.jpg';
            $largeFileName = 'user_large' . $ct . '.jpg';
            Input::file('thumb')->move(Config::get('constants.profilePicFilePath'), $thumbFileName);
            Input::file('large')->move(Config::get('constants.profilePicFilePath'), $largeFileName);
            return Response::json([
                    'pic_small' => [
                        'name' => $thumbFileName,
                        'path' => Config::get('constants.profilePicUrlPath') . $thumbFileName
                    ],
                    'pic_large' => [
                        'name' => $largeFileName,
                        'path' => Config::get('constants.profilePicUrlPath') . $largeFileName
                    ]
                ], self::STATUS_OK);
        } else {
            return Response::json(['errorMessage' => 'Photo not sent.  Please try again.'], self::STATUS_BAD_REQUEST);
        }
    }
    
    private function getNextNumber($fileLoc) {
        //$myfile = fopen($fileLoc, "r+");
        $count = file_get_contents($fileLoc);
        $count++;
        file_put_contents($fileLoc, $count);
        //fclose($myfile);
        //$count = (int)file_get_contents($fileLoc);
        //$count+=1;
        //file_put_contents($fileLoc,$count);
        return $count;
    }
    
    /**
     * Returns json form required for updating password.  For security, data
     * fields are always empty.
     * 
     * @return type
     */
    public function getPassword() {
        $response = [
            'values' => $this->getPasswordValues(),
            'meta' => $this->getPasswordMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    /**
     * Accepts json form with password data.
     * 
     * @return type
     */
    public function putPassword() {
        $validator = Validator::make(Input::all(), array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'new_password_again' => 'required|same:new_password'
        ));
        if ($validator->fails()) {
            //echo print_r(json_encode($validator->messages()));
            return Response::json($validator->messages(), self::STATUS_BAD_REQUEST);
        } else {
            //change password    
            $user = User::find(Auth::user()->id);
            $old_password = Input::get('old_password');
            $password = Input::get('new_password');
            if (Hash::check($old_password, $user->getAuthPassword())) {
                $user->password = Hash::make($password);
                if ($user->save()) {
                    return Response::json(['message' => 'success'], self::STATUS_OK);
                }
            } else {
                return Response::json(['errorMessage' => 'old password incorrect'], self::STATUS_NOT_FOUND);
            }
        }
        return Response::json(['error' => 'query failed'], self::STATUS_INTERNAL_SERVER_ERROR); 
    }
    
    /**
     * Updates a PERSON account.
     * 
     * @return type
     */
    public function putAccount() {
        //validate input
        $validator = Validator::make( Input::all(), $this->putAccountValidator() );
        if ($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        }
        //return error if account does not already exist
        if ( !AccountModel::accountExists('id', Input::get('user_id')) ) {
            return Response::json(['message' => 'user not found for ' . Input::get('user_id')], self::STATUS_BAD_REQUEST);
        }
        $success = AccountModel::updateAccount();
        if ($success !== true) {
            return Response::json($success, self::STATUS_NOT_FOUND);
        }
        return Response::json(['user' => Input::get('email')], self::STATUS_OK);
    }
    
    /**
     * Returns JSON form used to create or update a person account.  If a 
     * participant ID is provided, will contain current account values,
     * otherwise empty.  To modify account password, use getPassword method
     * instead.
     * 
     * @return type
     */   
    public function getAccount() {
        if (Input::has('participant_id')) {
            if (Input::get('participant_id') != Auth::user()->id) {
                return Response::json(['errorMessage' => 
                    "The account you're trying to access and the account you're logged in under don't match."], 
                    self::STATUS_BAD_REQUEST);
            }
            $values = $this->getAccountCurrentValues(Input::get('participant_id'));
        } else {
            $values = $this->getAccountDefaultValues();
        }
        $response = [
            'values' => $values,
            'meta' => $this->getAccountMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    /**
     * Deletes a USER account for provided user id.  Any associated person,
     * guest or group will also be deleted.
     * 
     * @return type
     */  
    public function deleteAccount() {
        if (!Input::has('user_id')) {
            return Response::json(['errorMessage' => "No account selected for deletion."], 
                    self::STATUS_BAD_REQUEST);
        }
        if (Input::get('user_id') != Auth::user()->id) {
                return Response::json(['errorMessage' => 
                    "The account you're trying to access and the account you're logged in under don't match."], 
                    self::STATUS_BAD_REQUEST);
            }
        $deleteStatus = AccountModel::deleteAccount(Input::get('user_id'));
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The account you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        return Response::json(['message' => 'Account deleted'], self::STATUS_OK);
    }
    
    private function postAccountValidator() {
        return array(
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_again' => 'required|same:password'
            //more validation
        ); 
    }
    
    private function putAccountValidator() {
        return array(
            'email' => 'required|email',
            //'password' => 'required|min:6',
            //'password_again' => 'required|same:password'
            //more validation
        ); 
    }
    
    private function getPasswordValues() {
        $values = [];
        $values['old_password'] = "";
        $values['new_password'] = "";
        $values['new_password_again'] = "";
        $values['_token'] = csrf_token();
        return $values;
    }
    
        private function getPasswordMeta() {
        $meta = [];
        $meta['old_password'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'Current Password', 
            'required'=>true
        ];
        $meta['new_password'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'New Password', 
            'minlength' => 6,
            'required' => true
        ];
        $meta['new_password_again'] = [
            'type' => 'string', 
            'input_type' => 'password_confirm',
            'name' => 'Reenter New Password', 
            'matches' => 'new_password',
            'required' => true
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden',
        ];
        return $meta;
    }
    
    private function getAccountDefaultValues() {
        $values = [];
        $values['email'] = "";
        $values['password'] = "";
        $values['password_again'] = "";
        $values['first_name'] = '';
        $values['last_name'] = '';
        $values['address1'] = '';
        $values['address2'] = '';
        $values['city'] = '';
        $values['state'] = '';
        $values['zip_code'] = '';
        $values['full_address'] = '';
        $values['latitude'] = 0.0;
        $values['longitude'] = 0.0;
        $values['street'] = '';
        $values['neighborhood'] = '';
        $values['phone_number'] = '';
        $values['phone_type'] = '';
        $values['share_email'] = "1";
        $values['share_address'] = "1";
        $values['share_phone'] = "0";
        $values['pic_large'] = '';
        $values['pic_small'] = '';
        $values['pic_large_url'] = Config::get('constants.genericProfilePicUrl');
        $values['pic_small_url'] = Config::get('constants.genericProfilePicUrl');
        $values['_token'] = csrf_token();
        return $values;
    }
    
     private function getAccountCurrentValues($userId) {
        $account = DB::table('users')
        ->join('participant', 'users.id', '=', 'participant.user_id')
        ->join('member', 'participant.participant_id', '=', 'member.member_id')
        ->join('person', 'person.person_id', '=', 'member.member_id')
        ->where('users.id', $userId)->first();
        $values = [];
        $values['user_id'] = $account->id;
        $values['member_id'] = $account->member_id;
        $values['email'] = $account->email;
        $values['first_name'] = $account->first_name;
        $values['last_name'] = $account->last_name;
        $values['address1'] = $account->address1;
        $values['address2'] = $account->address2;
        $values['city'] = $account->city;
        $values['state'] = $account->state;
        $values['zip_code'] = $account->zip_code;
        $values['full_address'] = $account->full_address;
        $values['latitude'] = $account->latitude;
        $values['longitude'] = $account->longitude;
        $values['street'] = $account->street;
        $values['neighborhood'] = $account->neighborhood;
        $values['phone_number'] = $account->phone_number;
        $values['phone_type'] = $account->phone_type;
        $values['share_email'] = $account->share_email;
        $values['share_address'] = $account->share_address;
        $values['share_phone'] = $account->share_phone;
        $values['pic_large'] = $account->pic_large;
        $values['pic_small'] = $account->pic_small;
        if ($account->pic_large) {
            $values['pic_large_url'] = Config::get('constants.profilePicUrlPath') 
                    . $account->pic_large;
        } else {
            $values['pic_large_url'] = Config::get('constants.genericProfilePicUrl');
        }   
        if ($account->pic_small) {
            $values['pic_small_url'] = Config::get('constants.profilePicUrlPath') 
                    . $account->pic_small;
        } else {
            $values['pic_small_url'] = Config::get('constants.genericProfilePicUrl');
        }
        $values['_token'] = csrf_token();
        return $values;
    }
    
 

    
    private function getAccountMeta() {
        $meta = [];
        $meta['email'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Email', 
            'pattern' => '/.+\@.+\..+/',
            'pattern_error' => 'Please enter a valid email address.',
            'required'=>true
        ];
        $meta['password'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'Password',
            'description' => 'Must be at least 6 characters',
            'minlength' => 6,
            'required' => true
        ];
        $meta['password_again'] = [
            'type' => 'string', 
            'input_type' => 'password_confirm',
            'name' => 'Reenter Password', 
            'required' => true,
            'matches' => 'password'
        ];
        $meta['first_name'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'First Name', 
            'required'=>true
        ];
        $meta['last_name'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Last Name', 
            'required'=>true
        ];
        $meta['address1'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Address Line 1',
            'required' => true
        ];
        $meta['address2'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Address Line 2'
        ];
        $meta['city'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'City',
            'required' => true
        ];
        $meta['state'] = [
            'type' => 'string',
            'input_type' => 'select',
            'options' => ['AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 
                    'AR'=>'Arkansas', 'CA'=>'California', 'CO'=>'Colorado', 
                    'CT'=>'Connecticut', 'DE'=>'Delaware', 'FL'=>'Florida', 
                    'GA'=>'Georgia', 'HI'=>'Hawaii', 'ID'=>'Idaho', 'IL'=>'Illinois', 
                    'IN'=>'Indiana', 'IA'=>'Iowa', 'KS'=>'Kansas', 'KY'=>'Kentucky', 
                    'LA'=>'Louisiana', 'ME'=>'Maine', 'MD'=>'Maryland', 
                    'MA'=>'Massachusetts', 'MI'=>'Michigan', 'MN'=>'Minnesota', 
                    'MS'=>'Mississippi', 'MO'=>'Missouri', 'MT'=>'Montana', 
                    'NE'=>'Nebraska', 'NV'=>'Nevada', 'NH'=>'New Hampshire', 
                    'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NY'=>'NewYork', 
                    'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio',
                    'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 
                    'RI'=>'Rhode Island', 'SC'=>'South Carolina', 
                    'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 
                    'UT'=>'Utah', 'VT'=>'Vermont', 'VA'=>'Virginia', 'WA'=>'Washington', 
                    'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming'
                ],
            'name' => 'State',
            'required' => true
        ];
        $meta['zip_code'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Zip Code',
            'required' => true
        ];
        $meta['full_address'] = [
            'type' => 'string',
            'input_type' => 'hidden',
            'name' => 'Full Address',
            'required' => true
        ];
        $meta['latitude'] = [
            'type' => 'float',
            'input_type' => 'hidden',
            'required' => true
        ];
        $meta['longitude'] = [
            'type' => 'float',
            'input_type' => 'hidden',
            'required' => true
        ];
        $meta['street'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Street Name',
            'required' => true
        ];
        $meta['neighborhood'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Neighborhood'
        ];
        $meta['phone_number'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Phone Number'
        ];
        $meta['phone_type'] = [
            'type' => 'string',
            'name' => 'Phone Type',
            'input_type' => 'select',
            'options' => ['mobile'=>'mobile','home'=>'home','work'=>'work']
        ];
        $meta['share_email'] = [
            'type' => 'boolean',
            'name' => 'Allow friends to see my email address',
            'input_type' => 'checkbox'
        ];
        $meta['share_address'] = [
            'type' => 'boolean',
            'name' => 'Allow friends to see my full address',
            'input_type' => 'checkbox'
        ];
        $meta['share_phone'] = [
            'type' => 'boolean',
            'name' => 'Allow friends to see my phone number',
            'input_type' => 'checkbox'
        ];
        $meta['pic_large'] = [
            'type' => 'text',
            'name' => 'Large Pic',
            'input_type' => 'hidden'
        ];
        $meta['pic_small'] = [
            'type' => 'text',
            'name' => 'Thumbnail',
            'input_type' => 'hidden'
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden',
        ];
        return $meta;
    }
    
    
}
