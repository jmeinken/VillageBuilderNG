<?php

class AlertModel {
    
    /**
     * Register an event with the alerts class.  The method will decided what
     * is the appropriate action for the event.
     * Currently, new friendships and new group-memberships should be registered.
     * 
     * Possible submissions:
     * 
     * @param type $table
     * @param type $pK
     */
    public static function registerEvent($table, $pK) {
        $json = json_encode(
            array(
                'relatedTable' => $table,
                'relatedRecord' => $pK
            )
        );
        if ($table == 'friendship') {
            $friendCheck = DB::table('friendship')
            ->where('person_id', '=', $pK['friend_id'])
            ->where('friend_id', '=', $pK['person_id'])
            ->first();
            if (is_null($friendCheck)) {
                $alertType = "friend request";
            } else {
                $alertType = "friend confirmation";
                // remove alert that was created when the friendship was requested
                $pK2 = array('person_id'=>$pK['friend_id'], 'friend_id'=>$pK['person_id']);
                AlertModel::unregisterEvent($table, $pK2);
            }
            DB::table('alert')->insert(
               array(
                   'participant_id' => $pK['friend_id'], 
                   'type' => $alertType,
                   'viewed' => 0,
                   'json' => $json
               )
            );
        }
        if ($table == 'group_member') {
            //check if person want to be member and has not been approved
            $membershipCheck = DB::table('group_member')
            ->where('person_id', '=', $pK['person_id'])
            ->where('group_id', '=', $pK['group_id'])
            ->first();
            if (!$membershipCheck->watching_only && !$membershipCheck->approved) {
                DB::table('alert')->insert(
                    array(
                        'participant_id' => $pK['group_id'], 
                        'type' => 'member request',
                        'viewed' => 0,
                        'json' => $json
                    )
                 );
            }
            if (!$membershipCheck->watching_only && $membershipCheck->approved) {
                AlertModel::unregisterEvent('group_member', $pK);
                DB::table('alert')->insert(
                    array(
                        'participant_id' => $pK['person_id'], 
                        'type' => 'member confirmation',
                        'viewed' => 0,
                        'json' => $json
                    )
                 );
            }
        }
    }
    
    public static function unregisterEvent($table, $pK) {
        $json = json_encode(
            array(
                'relatedTable' => $table,
                'relatedRecord' => $pK
            )
        );
        return DB::table('alert')->where('json', $json)->delete();
    }
    
    public static function deleteAlert($alertId) {
        return DB::table('alert')->where('alert_id', $alertId)->delete();
    }
    
    /**
     * Get all alerts for a particular participant.  Alerts can be for people, 
     * groups or guests.
     * 
     * @param type $participantId
     * @return type
     */
    public static function getAlerts($participantId) {
        $alerts = DB::table('alert')
            ->where('participant_id', '=', $participantId)
            ->get();
        $results = [];
        $i = 0;
        foreach ($alerts as $alert) {
            $results[$i]['alert_id'] = $alert->alert_id;
            $results[$i]['type'] = $alert->type;
            $results[$i]['viewed'] = $alert->viewed;
            $results[$i]['eventDate'] = $alert->created_on;
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
                //append supplementary info to output
                $results[$i]['participant_id'] = $record->person_id;
                $results[$i]['name'] = $record->first_name . " " . $record->last_name;
                $results[$i]['pic_small'] = $record->pic_small;
            }
            if ($json['relatedTable'] == "group_member" && $alert->type == 'member request') {
                //get supplementary info
                $record = DB::table('group_member')
                    ->join('person', 'person.person_id', '=', 'group_member.person_id')
                    ->join('member', 'member.member_id', '=', 'person.person_id')
                    ->where('group_member.person_id', '=', $json['relatedRecord']['person_id'])
                    ->where('group_member.group_id', '=', $json['relatedRecord']['group_id'])
                    ->first();
                if (!$record) {
                    continue;
                }
                //append supplementary info to output
                $results[$i]['participant_id'] = $record->person_id;
                $results[$i]['name'] = $record->first_name . " " . $record->last_name;
                $results[$i]['pic_small'] = $record->pic_small;
            }
            if ($json['relatedTable'] == "group_member" && $alert->type == 'member confirmation') {
                //get supplementary info
                $record = DB::table('group_member')
                    ->join('group', 'group.group_id', '=', 'group_member.group_id')
                    ->join('member', 'member.member_id', '=', 'group.group_id')
                    ->where('group_member.person_id', '=', $json['relatedRecord']['person_id'])
                    ->where('group_member.group_id', '=', $json['relatedRecord']['group_id'])
                    ->first();
                if (!$record) {
                    continue;
                }
                //append supplementary info to output
                $results[$i]['participant_id'] = $record->group_id;
                $results[$i]['name'] = $record->title;
                $results[$i]['pic_small'] = $record->pic_small;
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
    
    /** 
     * Marks all unviewed alerts for all participants associated with user as 
     * viewed.
     * 
     * @param type $userId
     */
    public static function markAlertsViewed($userId) {
        $result = DB::update('UPDATE alert SET viewed = 1 ' . 
                'WHERE participant_id IN (SELECT participant_id FROM participant ' . 
                'WHERE user_id = ' . $userId . ')');        
    }
    
    
    
}