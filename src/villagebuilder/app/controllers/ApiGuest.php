<?php

class ApiGuest extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   
    public function postGuest() {
        //validate input
        $validator = Validator::make( Input::all(), $this->postGuestValidator() );
        if ($validator->fails()) {
            return Response::json(['inputErrors' => $validator->messages()], self::STATUS_BAD_REQUEST);
        }
        //if email belongs to a member, reject
        
        //if email belongs to a guest, simply create person-guest relationship
        
        //insert record
        $requesterId = Auth::user()->id;
        $code = str_random(60);
        $success = GuestModel::createGuest(Input::get('email'),
                Input::get('first_name'),
                Input::get('last_name'),
                $code,
                $requesterId
        );
        //if the transaction failed, return error
        if ($success !== true) {
            return Response::json($success, 500);
        }
        return Response::json(['message' => 'Group Created'], self::STATUS_OK);
    }
    
    public function getGuest() {
        if (Input::has('participant_id')) {
            //$values = $this->getGroupCurrentValues(Input::get('participant_id'));
        } else {
            $values = $this->getGuestDefaultValues();
        }
        $response = [
            'values' => $values,
            'meta' => $this->getGuestMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    private function getGuestDefaultValues() {
        $values = [];
        $values['email'] = "";
        $values['first_name'] = '';
        $values['last_name'] = '';
        $values['_token'] = csrf_token();
        return $values;
    }
    
    private function getGuestMeta() {
        $meta = [];
        $meta['email'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Email', 
            'pattern' => '/.+\@.+\..+/',
            'pattern_error' => 'Please enter a valid email address.',
            'required'=>true
        ];
        $meta['first_name'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'First Name', 
            'required'=>false
        ];
        $meta['last_name'] = [
            'type' => 'string', 
            'input_type' => 'text',
            'name' => 'Last Name', 
            'required'=>false
        ];
        $meta['_token'] = [
            'type' => 'string',
            'input_type' => 'hidden',
        ];
        return $meta;
    }
    
    private function postGuestValidator() {
        return array(
            'email' => 'required|email',
            //more validation
        ); 
    }
    
}
