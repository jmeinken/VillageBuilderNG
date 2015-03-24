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
    
    
    
}
