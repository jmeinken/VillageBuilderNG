<?php

class ApiParticipant extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    

    
    public function getParticipant() {
        if (Input::has('participant_id')) {
            $response = ParticipantModel::getPublicParticipantInfo(Input::get('participant_id'));
        } else {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        return Response::json($response, self::STATUS_OK);
    }
    
        public function getCollectionNearbyPeople() {
        if (!Input::has('participant_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = ParticipantModel::getNearbyParticipants(Input::get('participant_id'));
        //if the transaction failed, return error
        return Response::json($result, self::STATUS_OK);
    }
    
    /**
     * Returns results of a search of people and groups for the provided search
     * string.
     * 
     * @return type
     */
    public function getCollectionSearchParticipants() {
        if (!Input::has('search_string')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = ParticipantModel::searchParticipants(Input::get('search_string'));
        //if the transaction failed, return error
        return Response::json($result, self::STATUS_OK);
    }
    
    
    
}
