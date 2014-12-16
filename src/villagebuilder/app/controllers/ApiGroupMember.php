<?php

class ApiGroupMember extends BaseController {
    
    //Dunbar's number is 150
    //Bernard–Killworth number is 290 mean, 231 median
    
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   
    public function postMembership() {
        if (!Input::has('participant_id') || !Input::has('group_id') 
            || !Input::has('watching_only')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $success = GroupMemberModel::createMembership(Input::get('participant_id'), 
                Input::get('group_id'),
                Input::get('watching_only')
        );
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        //register event with Alert Model
        //if (!Input::get('watching_only')) {
        //    AlertModel::registerEvent('membership', array('person_id' => Input::get('participant_id'),
        //        'group_id' => Input::get('group_id')));
        //}
        return Response::json(['message' => 'Group Membership Created'], self::STATUS_OK);
    }
    
    public function deleteMembership() {
        if (!Input::has('participant_id') || !Input::has('group_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $deleteStatus = GroupMemberModel::deleteMembership(Input::get('participant_id'), 
                Input::get('group_id')
        );
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The group membership you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        return Response::json(['message' => 'Group Membership deleted'], self::STATUS_OK);
    }
    
    public function putApproveMembership() {
        
    }
    
    public function putChangeMembershipType() {
        
    }
    
}

