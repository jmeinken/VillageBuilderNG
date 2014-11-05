<?php

class ApiAccount extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    
    
    private $postAccountValidator = array(
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_again' => 'required|same:password'
            //more validation
        ); 
    
   
    
    
    
    
    

    
    
    public function postAccount() {
        //validate input
        $validator = Validator::make( Input::all(), $this->postAccountValidator );
        if ($validator->fails()) {
            return Response::json($validator->messages(), self::STATUS_BAD_REQUEST);
        }
        //return error if account already exists
        if ( AccountModel::accountExists(Input::get('email')) ) {
            return Response::json(['message' => 'Email already taken'], self::STATUS_BAD_REQUEST);
        }
        //insert record for user, member and person
        $code = str_random(60);
        $success = AccountModel::createAccount($code);
        //if the transaction failed, return error
        if (!$success) {
            return Response::json(['message' => 'query failed'], self::STATUS_NOT_FOUND);
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
    
       
    public function getAccount() {
        $defaults = [];
        $defaults['email'] = "";
        $defaults['password'] = "";
        $defaults['password_again'] = "";
        $defaults['first_name'] = '';
        $defaults['last_name'] = '';
        $defaults['address1'] = '';
        $defaults['address2'] = '';
        $defaults['city'] = '';
        $defaults['state'] = '';
        $defaults['zip_code'] = '';
        $defaults['full_address'] = '';
        $defaults['latitude'] = 0.0;
        $defaults['longitude'] = 0.0;
        $defaults['street'] = '';
        $defaults['neighborhood'] = '';
        $defaults['phone_number'] = '';
        $defaults['phone_type'] = '';
        $defaults['pic_large'] = '';
        $defaults['pic_small'] = '';
        $defaults['_token'] = csrf_token();
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
        $response = [
            'defaults' => $defaults,
            'meta' => $meta
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    
    
    
}
