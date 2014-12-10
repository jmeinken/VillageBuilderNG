<?php

class AlertModel {
    public static function registerEvent($table, $pK) {
        if ($table == 'friendship') {
            //get data about person
            //$person = DB::table('member')
            //->join('person', 'person.person_id', '=', 'member.member_id')
            //->where('member_id', '=', $pK['person_id'])
            //->first();
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
            //if ($person->pic_small) {
            //    $person->profilePicThumbUrl = Config::get('constants.profilePicUrlPath') . 
            //            $person->pic_small;
            //} else {
            //    $person->profilePicThumbUrl = Config::get('constants.genericProfilePicUrl');
            //}
            $json = json_encode(
                array(
                    'relatedTable' => $table,
                    'relatedRecord' => $pK
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
    
    public static function getAlerts($participantId) {
        $alerts = DB::table('alert')
            ->where('participant_id', '=', $participantId)
            ->get();
        $results = [];
        $i = 0;
        foreach ($alerts as $alert) {
            //do processing
            $json = json_decode($alert->json, true);
            if ($json['relatedTable'] == "friendship") {
                //get supplementary info
                $record = DB::table('friendship')
                    ->join('person', 'person.person_id', '=', 'friendship.person_id')
                    ->join('member', 'member.member_id', '=', 'person.person_id')
                    ->where('friendship.person_id', '=', $json['relatedRecord']['person_id'])
                    ->where('friendship.friend_id', '=', $json['relatedRecord']['friend_id'])
                    ->first();
                if (!$record) {
                    continue;
                }
                //check if there is a reciprocal friendship
                $reciprocal = DB::table('friendship')
                    ->where('friendship.person_id', '=', $json['relatedRecord']['friend_id'])
                    ->where('friendship.friend_id', '=', $json['relatedRecord']['person_id'])
                    ->first();
                $results[$i]['type'] = ($reciprocal ? "friend_confirmation" : "friend_request");
                //append supplementary info to output
                $results[$i]['viewed'] = $alert->viewed;
                $results[$i]['eventDate'] = $alert->created_on;
                $results[$i]['firstName'] = $record->first_name;
                $results[$i]['lastName'] = $record->last_name;
                $results[$i]['firstName'] = $record->first_name;
                if ($record->pic_small) {
                   $results[$i]['profilePicThumbUrl'] = Config::get('constants.profilePicUrlPath') . 
                            $record->pic_small;
                } else {
                    $results[$i]['profilePicThumbUrl'] = Config::get('constants.genericProfilePicUrl');
                }
            }
            $i++;
        }
        return $results;
    }
    
    /*
    public static function getUnviewedAlertCount() {
        
    }
     *
     */
    
    public static function markAlertsViewed($participantId) {
        return DB::table('alert')
            ->where('participant_id', $participantId)
            ->update(array(
                'viewed' => 1
            )
        );
    }
    
    
    
}