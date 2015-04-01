<?php

class ApiFriendship extends BaseController {
    
    //Dunbar's number is 150
    //Bernard–Killworth number is 290 mean, 231 median
    
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
    /**
     * Returns all friends ('reciprocated', 'unconfirmed' or 'requested') for
     * the provided person.
     * 
     * @return type
     */
    public function getCollectionFriendship() {
        if (!Input::has('participant_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = FriendshipModel::getFriends(Input::get('participant_id'));
        //if the transaction failed, return error
        if ($result === false) {
            return Response::json('query failed', 500);
        }
        return Response::json($result, self::STATUS_OK);
    }
    
    public function getCollectionFriendRequests() {
        
    }
    

    /**
     * 
     * Creates a new friendship between two people (not guest friendships).
     * 
     * @return type
     */
    public function postFriendship() {
        if (!Input::has('participant_id') || !Input::has('friend_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $success = FriendshipModel::createFriendship(Input::get('participant_id'), 
                Input::get('friend_id')
        );
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        //register event with Alert Model
        AlertModel::registerEvent('friendship', array('person_id' => Input::get('participant_id'),
            'friend_id' => Input::get('friend_id')));
        return Response::json(['message' => 'Friendship Created'], self::STATUS_OK);
    }
    
    /**
     * !!UNFINISHED.  Currently only tells status without actually updating
     * the database.
     * 
     * Creates a new friendship with the current logged in user using the 
     * friend's email address instead of an id.  Works for PERSON and GUEST
     * relationships.  
     * Responses: 
     * guest added
     * already friends
     * friendship added
     * already guest friend
     * guest friendship added
     * 
     * @return type
     */
    public function getFriendshipUsingEmail() {
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
            if ($friendMemberStatus=='none') {
                $friendshipType = 'none';
                $participant_info = new stdClass();
            } else {
                $friendshipType = FriendshipModel::getFriendshipType($participantId, $friendId);
                $participant_info = ParticipantModel::getPublicParticipantInfo($friendId);
            }
            $response[] = array('request'=>$request['request'], 
                'member_status'=>$friendMemberStatus, 
                'relationship_status'=> $friendshipType,
                'participant_info' => $participant_info
            );
        }
        return Response::json(['message' => $response], self::STATUS_OK);
    }
    
    public function postFriendshipUsingEmail() {
        if (!Input::has('email')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $email = Input::get('email');
        $participantId = UserModel::getParticipantIdsForUser(Auth::user()->id);
        $friendId = UserModel::getGuestOrPersonIdByEmail($email);
        $friendUserType = UserModel::getMemberStatusByEmail($email);
        if ($friendUserType=='none') {
            //add new guest and guest friendship
            $response = array('email'=>$email, 'action'=>'guest added');
        }
        if ($friendUserType=='member') {
            $friendshipType = FriendshipModel::getFriendshipType($participantId, $friendId);
            if ($friendshipType == "reciprocated" || $friendshipType == "unconfirmed") {
                $response = array('email'=>$email, 'action'=>'already friend');
            }
            if ($friendshipType == "none" || $friendshipType == "requested") {
                //add friendship
                $response = array('email'=>$email, 'action'=>'friendship added');
            }
        }
        if ($friendUserType=='guest') {
            $friendshipType = FriendshipModel::getFriendshipType($participantId, $friendId);
            if ($friendshipType == "guest") {
                $response = array('email'=>$email, 'action'=>'already guest friend');
            }   
            if ($friendshipType == "none") {
                //add guest friendship
                $response = array('email'=>$email, 'action'=>'guest friendship added');
            }   
        } 
        if (!isset($response)) {
            $response = "friendUserType:" . $friendUserType . "; friendshipType:" . $friendshipType;
            return Response::json(['errorMessage' => $response], 
                    self::STATUS_BAD_REQUEST);
        }
        return Response::json(['message' => $response], self::STATUS_OK);
    }
    
   /*
    public function postFriendshipsUsingEmails() {
        if (!Input::has('participant_id') || !Input::has('friend_array')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $friends = json_decode ( Input::get('friend_array'), true );
        $response = [];
        foreach ($friends as $friend) {
            if (!$friend['email']) {
                continue;
            }
            $friendUserType = UserModel::getUserTypeByEmail($friend['email']);
            if ($friendUserType=='none') {
                //add new guest and guest friendship
                $response[] = array('email'=>$friend['email'], 'action'=>'guest added');
                continue;
            }
            if ($friendUserType=='person') {
                $friendshipType = FriendshipModel::getFriendshipType(Input::get('participant_id'), $person->person_id);
                if ($friendshipType == "reciprocated" || $friendshipType == "unconfirmed") {
                    $response[] = array('email'=>$friend['email'], 'action'=>'already friend');
                    continue;
                }
                if ($friendshipType == "none" || $friendshipType == "requested") {
                    //add friendship
                    $response[] = array('email'=>$friend['email'], 'action'=>'friendship added');
                    continue;
                }
            }
            if ($friendUserType=='guest') {
                $friendshipType = FriendshipModel::getFriendshipType(Input::get('participant_id'), $person->person_id);
                if ($friendshipType == "guest") {
                    $response[] = array('email'=>$friend['email'], 'action'=>'already guest friend');
                    continue;
                }   
                if ($friendshipType == "none") {
                    //add guest friendship
                    $response[] = array('email'=>$friend['email'], 'action'=>'guest friendship added');
                    continue;
                }   
            } 
        }
    }
    * 
    */
    
    /**
     * Deletes a frienship (not guest friendship)
     * 
     * @return type
     */
    public function deleteFriendship() {
        if (!Input::has('participant_id') || !Input::has('friend_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $deleteStatus = FriendshipModel::deleteFriendship(Input::get('participant_id'), 
                Input::get('friend_id')
        );
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The friendship you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        AlertModel::unregisterEvent('friendship', array('person_id' => Input::get('participant_id'),
            'friend_id' => Input::get('friend_id')));
        return Response::json(['message' => 'Friendship deleted'], self::STATUS_OK);
    }
    
}
