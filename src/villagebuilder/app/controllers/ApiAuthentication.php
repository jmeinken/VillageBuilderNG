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
            $response['user_type'] = 'member';
            //$response['_token'] = csrf_token();
            $response['userId'] = Auth::user()->id;
            $response['email'] = Auth::user()->email;
            //send more data about user
            $account = DB::table('users')
            ->join('participant', 'users.id', '=', 'participant.user_id')
            ->join('member', 'participant.participant_id', '=', 'member.member_id')
            ->join('person', 'person.person_id', '=', 'member.member_id')
            ->where('users.id', Auth::user()->id)->first();
            $response['firstName'] = $account->first_name;
            $response['lastName'] = $account->last_name;
            if ($account->pic_large == "") {
                $response['profilePicFile'] = "";
                $response['profilePicUrl'] = "assets/images/generic-user.png";
            } else {
                $response['profilePicFile'] = $account->pic_large;
                $response['profilePicUrl'] = Config::get('constants.profilePicUrlPath') . $account->pic_large;
            }
            if ($account->pic_small == "") {
                $response['profilePicThumbFile'] = "";
                $response['profilePicThumbUrl'] = "assets/images/generic-user.png";
            } else {
                $response['profilePicThumbFile'] = $account->pic_small;
                $response['profilePicThumbUrl'] = Config::get('constants.profilePicUrlPath') . $account->pic_small;
            }
            ////////////////////////////
            return Response::json($response, self::STATUS_OK);
        } else {
            $response = [];
            $response['logged_in'] = false;
            return Response::json($response, self::STATUS_OK);
        }
    }
    
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
    
    public function postLogOut() {
        Auth::logout();
        return Response::json(['message' => 'logged out'], self::STATUS_OK);
    }
    
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
    
    public function getResetPassword() {
        $response = [
            'values' => $this->getResetPasswordValues(),
            'meta' => $this->getResetPasswordMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
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

