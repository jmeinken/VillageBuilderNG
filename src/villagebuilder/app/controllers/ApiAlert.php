<?php

class ApiAlert extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
    public function getCollectionAlert() {
        if (!Input::has('participant_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = AlertModel::getAlerts(Input::get('participant_id'));
        return Response::json($result, self::STATUS_OK);
    }
    
    public function postResetUnviewedAlertCount() {
        if (!Input::has('participant_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $result = AlertModel::markAlertsViewed(Input::get('participant_id'));
        return Response::json($result, self::STATUS_OK);
    }
    
    
    
    
}
