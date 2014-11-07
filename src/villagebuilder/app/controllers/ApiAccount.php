<?php

class ApiAccount extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    
   
    public function postAccount() {
        //validate input
        $validator = Validator::make( Input::all(), $this->postAccountValidator() );
        if ($validator->fails()) {
            return Response::json($validator->messages(), self::STATUS_BAD_REQUEST);
        }
        //return error if account already exists
        if ( AccountModel::accountExists('email', Input::get('email')) ) {
            return Response::json(['errorMessage' => 'Email already taken'], self::STATUS_FORBIDDEN);
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
                'link' =>  'http://johnmeinken.com/vb-dev/src/villagebuilder/ng/#/activate_account/' . $code,
                'username' => $name                
            ), function($message) use ($email, $name) {
                $message->to($email, $name)->subject('Activate account');
            }
        );
        //return success response
        return Response::json(['user' => Input::get('email')], self::STATUS_OK);
    }
    
    public function getPassword() {
        $response = [
            'values' => $this->getPasswordValues(),
            'meta' => $this->getPasswordMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
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
        return Response::json(['errorMessage' => 'failed'], self::STATUS_NOT_FOUND); 
    }
    
    public function putAccount() {
        //validate input
        $validator = Validator::make( Input::all(), $this->putAccountValidator() );
        if ($validator->fails()) {
            return Response::json($validator->messages(), self::STATUS_BAD_REQUEST);
        }
        //return error if account does not already exist
        if ( !AccountModel::accountExists('id', Input::get('user_id')) ) {
            return Response::json(['message' => 'user not found'], self::STATUS_BAD_REQUEST);
        }
        $success = AccountModel::updateAccount();
        if ($success !== true) {
            return Response::json($success, self::STATUS_NOT_FOUND);
        }
        return Response::json(['user' => Input::get('email')], self::STATUS_OK);
    }
    
       
    public function getAccount() {
        if (Input::has('user_id')) {
            $values = $this->getAccountCurrentValues(Input::get('user_id'));
        } else {
            $values = $this->getAccountDefaultValues();
        }
        $response = [
            'values' => $values,
            'meta' => $this->getAccountMeta()
        ];
        return Response::json($response, self::STATUS_OK);
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
            'required' => true
        ];
        $meta['new_password_again'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'Reenter New Password', 
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
        $values['email'] = "a";
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
        $values['pic_large'] = '';
        $values['pic_small'] = '';
        $values['_token'] = csrf_token();
        return $values;
    }
    
    private function getAccountCurrentValues($userId) {
        $account = DB::table('users')
            ->join('member', 'users.id', '=', 'member.user_id')
            ->join('person', 'person.member_id', '=', 'member.member_id')
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
        $values['pic_large'] = $account->pic_large;
        $values['pic_small'] = $account->pic_small;
        $values['_token'] = csrf_token();
        return $values;
    }
    

    
    private function getAccountMeta() {
        $meta = [];
        $meta['email'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Email', 
            'required'=>true
        ];
        $meta['password'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'Password', 
            'required' => true
        ];
        $meta['password_again'] = [
            'type' => 'string', 
            'input_type' => 'password',
            'name' => 'Reenter Password', 
            'required' => true
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
            'required' => true,
            'maxLength' => 2
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
        $meta['pic_large'] = [
            'type' => 'file',
            'name' => 'Email'
        ];
        $meta['pic_small'] = [
            'type' => 'file',
            'name' => 'Email'
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden',
        ];
        return $meta;
    }
    
    
}
