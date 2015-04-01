<?php

class ApiMembership extends BaseController {
    
    //Dunbar's number is 150
    //Bernard–Killworth number is 290 mean, 231 median
    
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   
    /**
     * Creates an inactive membership and registers an alert 
     * 
     * @return type
     */
    public function postMembership() {
        if (!Input::has('participant_id') || !Input::has('group_id') 
            || !Input::has('watching_only')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $relationshipAlreadyExists = MembershipModel::checkMembership(Input::get('participant_id'), Input::get('group_id'));
        if ($relationshipAlreadyExists) {
            $success = MembershipModel::alterMembership(Input::get('participant_id'), 
                    Input::get('group_id'),
                    Input::get('watching_only')
            );
        } else {
            $success = MembershipModel::createMembership(Input::get('participant_id'), 
                    Input::get('group_id'),
                    Input::get('watching_only')
            );
        }
        //if the transaction failed, return error
        if (!$success) {
            return Response::json('query failed', 500);
        }
        //register event with Alert Model
        AlertModel::registerEvent('group_member', array('person_id' => Input::get('participant_id'),
            'group_id' => Input::get('group_id')));
        return Response::json(['message' => 'Group Membership Created'], self::STATUS_OK);
    }
    
    public function deleteMembership() {
        if (!Input::has('participant_id') || !Input::has('group_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $deleteStatus = MembershipModel::deleteMembership(Input::get('participant_id'), 
                Input::get('group_id')
        );
        if (!$deleteStatus) {
            return Response::json(['errorMessage' => 
                    "The group membership you're trying to delete doesn't exist."], 
                    self::STATUS_NOT_FOUND);
        }
        $pK = array('person_id' => Input::get('participant_id'), 'group_id' => Input::get('group_id'));
        AlertModel::unregisterEvent('group_member', $pK);
        return Response::json(['message' => 'Group Membership deleted'], self::STATUS_OK);
    }
    
    /**
     * Changed a membership from inactive to active
     */
    public function putApproveMembership() {
        if (!Input::has('participant_id') || !Input::has('group_id')) {
            return Response::json(['errorMessage' => 'Query missing required value'], 
                    self::STATUS_BAD_REQUEST);
        }
        $approveStatus = MembershipModel::approveMembership(Input::get('participant_id'), 
                Input::get('group_id'));
        if (!$approveStatus) {
            return Response::json(['errorMessage' => 
                    "There was a problem approving this member."], 
                    self::STATUS_NOT_FOUND);
        }
        $pK = array('person_id' => Input::get('participant_id'), 'group_id' => Input::get('group_id'));
        AlertModel::registerEvent('group_member', $pK);
        return Response::json(['message' => 'Group Membership approved'], self::STATUS_OK);
    }
    
    /*
     * changes a membership type (watching only vs full member)
     */
    public function putChangeMembershipType() {
        
    }
    
    
    
}

