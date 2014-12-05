<?php

class ApiGroup extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   
    public function postGroup() {
        //validate input
        $validator = Validator::make( Input::all(), $this->postGroupValidator() );
        if ($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        }
        //insert record for user, member and person
        $userId = Auth::user()->id;
        $success = GroupModel::createGroup($userId);
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        return Response::json(['message' => 'Group Created'], self::STATUS_OK);
    }
    
    public function putGroup() {
        //validate input
        $validator = Validator::make( Input::all(), $this->putGroupValidator() );
        if ($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        }
        //return error if account does not already exist
        if ( !GroupModel::groupExists('group_id', Input::get('member_id')) ) {
            return Response::json(['message' => 'group not found'], self::STATUS_BAD_REQUEST);
        }
        $success = GroupModel::updateGroup();
        if ($success !== true) {
            return Response::json($success, self::STATUS_NOT_FOUND);
        }
        return Response::json(['message' => 'group data updated'], self::STATUS_OK);
    }
    
       
    public function getGroup() {
        if (Input::has('participant_id')) {
            //if (Input::get('member_id') != Auth::user()->id) {
            //    return Response::json(['errorMessage' => 
            //        "The account you're trying to access and the account you're logged in under don't match."], 
            //        self::STATUS_BAD_REQUEST);
            //}
            $values = $this->getGroupCurrentValues(Input::get('participant_id'));
        } else {
            $values = $this->getGroupDefaultValues();
        }
        $response = [
            'values' => $values,
            'meta' => $this->getGroupMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
      
    public function deleteGroup() {
        if (!Input::has('participant_id')) {
            return Response::json(['errorMessage' => "No group selected for deletion."], 
                    self::STATUS_BAD_REQUEST);
        }
        //if (Input::get('user_id') != Auth::user()->id) {
        //        return Response::json(['errorMessage' => 
        //            "The account you're trying to access and the account you're logged in under don't match."], 
        //            self::STATUS_BAD_REQUEST);
        //    }
        $deleteStatus = GroupModel::deleteGroup(Input::get('participant_id'));
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The group you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        return Response::json(['message' => 'Group deleted'], self::STATUS_OK);
    }
    
    private function postGroupValidator() {
        return array(
            'email' => 'required|email',
            //more validation
        ); 
    }
    
    private function putGroupValidator() {
        return array(
            'email' => 'required|email',
            //'password' => 'required|min:6',
            //'password_again' => 'required|same:password'
            //more validation
        ); 
    }
 
    private function getGroupDefaultValues() {
        $values = [];
        $values['email'] = "";
        $values['title'] = '';
        $values['description'] = '';
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
    
    private function getGroupCurrentValues($memberId) {
        $account = DB::table('member')
        ->join('group', 'group.group_id', '=', 'member.member_id')
        ->where('member.member_id', $memberId)->first();
        $values = [];
        $values['member_id'] = $memberId;
        $values['email'] = $account->email;
        $values['title'] = $account->title;
        $values['description'] = $account->description;
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
    

    private function getGroupMeta() {
        $meta = [];
        $meta['email'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Email', 
            'pattern' => '/.+\@.+\..+/',
            'pattern_error' => 'Please enter a valid email address.',
            'required'=>true
        ];
        $meta['title'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Group Name', 
            'required'=>true
        ];
        $meta['description'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Group Description', 
            'required'=>false
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
