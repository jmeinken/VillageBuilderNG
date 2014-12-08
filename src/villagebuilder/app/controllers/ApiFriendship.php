<?php

class ApiFriendship extends BaseController {
    
    //Dunbar's number is 150
    //Bernard–Killworth number is 290 mean, 231 median
    
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
    public function getCollectionFriendship() {
        if (!Input::has('person_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = FriendshipModel::getFriends(Input::get('person_id'));
        //if the transaction failed, return error
        if (!$result) {
            return Response::json('query failed', 500);
        }
        return Response::json($result, self::STATUS_OK);
    }
    
    public function getCollectionFriendRequests() {
        
    }
    
    public function getCollectionNearbyPeople() {
        if (!Input::has('person_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = FriendshipModel::getNearbyPeople(Input::get('person_id'));
        //if the transaction failed, return error
        if (!$result) {
            return Response::json('query failed', 500);
        }
        return Response::json($result, self::STATUS_OK);
    }
    
    public function postFriendship() {
        if (!Input::has('person_id') || !Input::has('friend_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $success = FriendshipModel::createFriendship(Input::get('person_id'), 
                Input::get('friend_id')
        );
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        return Response::json(['message' => 'Friendship Created'], self::STATUS_OK);
    }
    
    public function deleteFriendship() {
        if (!Input::has('person_id') || !Input::has('friend_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $deleteStatus = FriendshipModel::deleteFriendship(Input::get('person_id'), 
                Input::get('friend_id')
        );
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The friendship you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        return Response::json(['message' => 'Friendship deleted'], self::STATUS_OK);
    }
    
}
