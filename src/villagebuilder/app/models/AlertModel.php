<?php

class AlertModel {
    public static function registerEvent($table, $pK) {
        if ($table == 'friendship') {
            //get data about person
            $person = DB::table('member')
            ->join('person', 'person.person_id', '=', 'member.member_id')
            ->where('member_id', '=', $pK['person_id'])
            ->first();
            // check if reciprocal friendship already exists
            $friendCheck = DB::table('friendship')
            ->where('person_id', '=', $pK['friend_id'])
            ->where('friend_id', '=', $pK['person_id'])
            ->first();
            if (is_null($friendCheck)) {
                $alertType = "friend request";
            } else {
                $alertType = "friend confirmation";
            }
            //add record to alert table
            if ($person->pic_small) {
                $person->profilePicThumbUrl = Config::get('constants.profilePicUrlPath') . 
                        $person->pic_small;
            } else {
                $person->profilePicThumbUrl = Config::get('constants.genericProfilePicUrl');
            }
            $json = json_encode(
                array(
                    'relatedTable' => $table,
                    'relatedRecord' => $pK,
                    'firstName' => $person->first_name,
                    'lastName' => $person->last_name,
                    'profilePicThumbUrl' => $person->profilePicThumbUrl
                )
            );
            DB::table('alert')->insert(
               array(
                   'participant_id' => $pK['friend_id'], 
                   'type' => $alertType,
                   'viewed' => 0,
                   'json' => $json
               )
            );
        }
    }
    
    public static function deleteAlert() {
        
    }
    
    public static function getAlerts() {
        
    }
    
    /*
    public static function getUnviewedAlertCount() {
        
    }
     *
     */
    
    public static function markAlertsViewed() {
        
    }
    
    
    
}