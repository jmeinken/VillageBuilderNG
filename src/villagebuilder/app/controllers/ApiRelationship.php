<?php


/**
 * This class contains methods that don't fall neatly into ApiFriendship,
 * ApiGuest or ApiMembership
 */
class ApiRelationship extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   

    /**
     * Form for adding a relationship using email.  Output from this form should
     * be grouped into an array 'request'.  Rather than going directly to post,
     * there is an interim verification method that the request should be 
     * passed to.  Currently only works for members and guests, not groups.
     * 
     * @return type
     */
    public function getAddRelationshipByEmail() {
        if (Input::has('participant_id')) {
            //$values = $this->getGroupCurrentValues(Input::get('participant_id'));
        } else {
            $values = $this->getAddRelationshipByEmailDefaultValues();
        }
        $response = [
            'values' => $values,
            'meta' => $this->getAddRelationshipByEmailMeta()
        ];
        return Response::json($response, self::STATUS_OK);
    }
    
    /**
     * Accepts an array of getAddRelationshipByEmail forms.  Returns a list
     * of member and relationship statuses for the provided emails and 
     * participant info when available.
     * 
     * @return type
     */
    public function getAddRelationshipByEmailVerify() {
        //return Response::json(['message' => json_decode(Input::get('request'))], self::STATUS_OK);
        if (!Input::has('request')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $requests = json_decode(Input::get('request'), true);
        $participantId = UserModel::getPersonOrGuestForUser(Auth::user()->id);
        foreach($requests as $request) {
            $email = $request['request']['email'];
            $friendId = UserModel::getGuestOrPersonIdByEmail($email);
            $friendMemberStatus = UserModel::getMemberStatusByEmail($email);
            if ($friendMemberStatus == ParticipantTypes::NONE) {
                $friendshipType = Relationships::NONE;
                $participant_info = new stdClass();
            } else {
                $friendshipType = FriendshipModel::getFriendshipType($participantId, $friendId);
                $participant_info = ParticipantModel::getPublicParticipantInfo($friendId);
            }
            $response[] = array('request'=>$request['request'], 
                'member_status'=>$friendMemberStatus, 
                'relationship_status'=> $friendshipType,
                'confirm' => true,
                'participant_info' => $participant_info
            );
        }
        return Response::json([$response], self::STATUS_OK);
    }
    
    /**
     * Accepts output of getAddRelationshipByEmailVerify.  For each participant
     * where 'addRelationship' is true, will add the relationship.  Ignores
     * entry if false. 
     * Possible member_status: member, guest, none
     * Possible relationship_status: request received, guest, none 
     * (reciprocated, requesting will also be handled by being ignored)
     */
    public function postAddRelationshipByEmail() {
        //return Response::json(['message' => json_decode(Input::get('request'))], self::STATUS_OK);
        if (!Input::has('request')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $requests = json_decode(Input::get('request'), true);
        //return Response::json($requests, self::STATUS_OK);
        $participantId = UserModel::getPersonOrGuestForUser(Auth::user()->id);
        $response = array();
        foreach($requests as $request) {
            if (!$request['confirm']) {
                continue;
            }
            if ($request['member_status'] == ParticipantTypes::NONE) {
                //create guest
                //create guest relationship
                $code = str_random(60);
                $response[] = $request['request']['email'];
                $response[] = GuestModel::createGuest($request['request']['email'], 
                        $request['request']['first_name'], 
                        $request['request']['last_name'], 
                        $code, 
                        $participantId
                );
                $response[] = "create guest and guest relationship";
            } elseif ($request['member_status'] == ParticipantTypes::GUEST) {
                if ($request['relationship_status'] == 'none') {
                    //create guest relationship
                    GuestModel::createGuestRelationship($participantId, $request['participant_info']['participant_id']);
                    $response[] = "create guest relationship";
                }
            } elseif ($request['member_status'] == ParticipantTypes::MEMBER) {
                if ($request['relationship_status'] == 'request received' || 
                        $request['relationship_status'] == 'none') {
                    //create friendship
                    createFriendship($participantId, $request['participant_info']['participant_id']);
                    $response[] = "create friendship";
                }
            }
        }
        return Response::json(['message' => $response], self::STATUS_OK);
    }
    
    private function getAddRelationshipByEmailDefaultValues() {
        $values = [];
        $values['email'] = "";
        $values['first_name'] = '';
        $values['last_name'] = '';
        $values['_token'] = csrf_token();
        return $values;
    }
    
    private function getAddRelationshipByEmailMeta() {
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
            'required'=>true
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
    

    
}


