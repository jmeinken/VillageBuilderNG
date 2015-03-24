<?php

class ApiAuthentication extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
    public function checkLoginStatus() {
        if (Auth::check()) {
            $response = [];
            $response['logged_in'] = true;
            $response['userId'] = Auth::user()->id;
            $response['participants'] = UserModel::getParticipantsForUser($response['userId']);
            foreach ($response['participants'] as $key => $participant) {
                if ($participant->participant_type == 'person') {
                    $response['participants'][$key]->friendships = FriendshipModel::getFriendships($participant->participant_id);
                    $response['participants'][$key]->memberships = MembershipModel::getMemberships($participant->participant_id);
                    $response['participants'][$key]->ownerships = MembershipModel::getOwnerships($participant->participant_id, Auth::user()->id);
                    $response['participants'][$key]->friendCollection = FriendshipModel::getFriendCollection($participant->participant_id);
                    $response['participants'][$key]->membershipCollection = MembershipModel::getMembershipCollection($participant->participant_id);
                } else if ($participant->participant_type == 'group') {
                    $response['participants'][$key]->memberCollection = MembershipModel::getMemberCollection($participant->participant_id);
                    $response['participants'][$key]->members = MembershipModel::getMembers($participant->participant_id);
                    $response['participants'][$key]->owner = MembershipModel::getOwner(Auth::user()->id);
                }
            }
            return Response::json($response, self::STATUS_OK);
        } else {
            $response = [];
            $response['logged_in'] = false;
            return Response::json($response, self::STATUS_OK);
        }
    }
    

    
    /**
     * Returns JSON string for login form.
     * 
     * @return type
     */
    public function getLogIn() {
        $values = [];
        $values['email'] = "thebellsofohio@hotmail.com";
        $values['password'] = "testtest";
        $values['remember'] = false;
        $values['_token'] = csrf_token();
        $meta = [];
        $meta['email'] = [
            'type' => 'string',
            'input_type' => 'text',
            'name' => 'Email',
            'required' => true,
        ];
        $meta['password'] = [
            'type' => 'string',
            'input_type' => 'password',
            'name' => 'Password',
            'required' => true
        ];
        $meta['remember'] = [
            'type' => 'boolean',
            'input_type' => 'checkbox',
            'name' => 'Remember Me'
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden'
        ];
        $response = [
            'values' => $values,
            'meta' => $meta
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    /**
     * accepts JSON login string.  Returns user id and user email if login
     * successful.
     * 
     * @return type
     */
    public function postLogIn() {
        $validator = Validator::make(Input::all(),
            array(
                'email' => 'required|email',
                'password' => 'required'
            ) 
        );
        if ($validator->fails()) {
            //redirect back to sign-in
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        } else {
            $remember = (Input::get('remember')=="true" ? true : false);
            //attempt user sign in
            $auth = Auth::attempt(array(
                'email' => Input::get('email'),
                'password' => Input::get('password'),
                'active' => 1
            ), $remember);
            if ($auth) {
                //send user back to where they wanted to go
                $response = [];
                $response['user_id'] = Auth::user()->id;
                $response['user_email'] = Auth::user()->email;
                return Response::json($response, self::STATUS_OK);
            }
        }
        return Response::json(['errorMessage' => 'Unable to log in.  Check your email and password.'], self::STATUS_NOT_FOUND);
    }
    
    /**
     * logs out the current user
     * 
     * @return type
     */
    public function postLogOut() {
        Auth::logout();
        return Response::json(['message' => 'logged out'], self::STATUS_OK);
    }
    
    /**
     * Accounts are initially created as inactivated.  This will check for 
     * the appropriate activation code and activate the account.
     * 
     * @return type
     */
    public function postActivateAccount() {
        //validate input code
        $validator = Validator::make( Input::all(), array(
            'code' => 'required|min:60|max:60'
        ) );
        if ($validator->fails()) {
            return Response::json(['errorMessage' => "There was a problem with the account activation code. Did you copy the entire link?"], self::STATUS_BAD_REQUEST);
        }
        $user = User::where('code', '=', Input::get('code'))->where('active', '=', 0);
        //send error if no match in database
        if(!$user->count()) {
            return Response::json(['errorMessage' => "Unable to activate account.  Has this account already been activated?"], self::STATUS_NOT_FOUND);
        }
        //activate account and reset code
        $user = $user->first();
        $user->active = 1;
        $user->code = "";
        if($user->save()) {
            return Response::json(['message' => "account activated"], self::STATUS_OK);
        }
        return Response::json(['error' => "unknown error"], self::STATUS_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Activates the temporary password during a password reset (for forgot 
     * password)     * 
     * 
     * @return type
     */
    public function postActivateResetPassword() {
        //validate input code
        $validator = Validator::make( Input::all(), array(
            'code' => 'required|min:60|max:60'
        ) );
        if ($validator->fails()) {
            return Response::json(['errorMessage' => "There was a problem with the reset password activation code. Did you copy the entire link?"], self::STATUS_BAD_REQUEST);
        }
        $user = User::where('code', '=', Input::get('code'))->where('password_temp', '!=', '');
        //send error if no match in database
        if(!$user->count()) {
            return Response::json(['errorMessage' => "Unable to activate new password.  Has this password already been activated?"], self::STATUS_NOT_FOUND);
        }
        //activate account and reset code
        $user = $user->first();
        $user->password = $user->password_temp;
        $user->password_temp = '';
        $user->code = '';
        if ($user->save()) {
           return Response::json(['message' => "account activated"], self::STATUS_OK);
        } 
        return Response::json(['error' => "unknown error"], self::STATUS_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Returns the password reset (forgot password) form
     * 
     * @return type
     */
    public function getResetPassword() {
        $response = [
            'values' => $this->getResetPasswordValues(),
            'meta' => $this->getResetPasswordMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    /**
     * Sends an email for resetting the password.  The password doesn't actually
     * get reset until postActivateResetPassword is called.
     * 
     * @return type
     */
    public function postResetPassword() {
        $validator = Validator::make(Input::all(), array(
            'email' => 'required|email'
        ));
        if($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        } else {
            //change password
            $user = User::where('email', '=', Input::get('email'));
            if ($user->count()) {
                $user = $user->first();
                $code = str_random(60);
                $password = str_random(10);
                $user->code = $code;
                $user->password_temp = Hash::make($password);
                if ($user->save()) {
                    Mail::send('emails.auth.recover',
                        array(
                            'link' =>  'http://johnmeinken.com/vb-dev/src/villagebuilder/ng/#/activate-reset-password/' . $code,
                            'username' => $user->username,
                            'password' => $password
                        ),
                        function($message) use ($user) {
                            $message->to($user->email, $user->username)
                                    ->subject('Your new password');
                        }              
                    );
                    return Response::json(['message' => 'success'], self::STATUS_OK);
                }
            }
        }
        return Response::json(['errorMessage' => 'Failed to reset password.  Did you enter the correct email address?'], self::STATUS_NOT_FOUND); 
    }
    
    
    private function getResetPasswordValues() {
        $values = [];
        $values['email'] = "";
        $values['_token'] = csrf_token();
        return $values;
    }
    
    private function getResetPasswordMeta() {
        $meta = [];
        $meta['email'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Email Address', 
            'required'=>true
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden',
        ];
        return $meta;
    }
    
   
    
    
    
    
}

