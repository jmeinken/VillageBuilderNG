<?php

class ParticipantModel {
    
    /**
     * For provided participant_id, determines if that participant is a "person",
     * "guest", "group" or "none"
     * 
     * @param type $participantId
     * @return string
     */
    public static function getParticipantTypeById($participantId) {
        //returns guest, person, group, none
        $sql = "SELECT * FROM person WHERE person_id=?";
        $person = DB::select($sql, array($participantId))->first();
        if (!is_null($person)) {
            return "person";
        }
        $sql = "SELECT * FROM group WHERE group_id=?";
        $group = DB::select($sql, array($participantId))->first();
        if (!is_null($group)) {
            return "group";
        }
        $sql = "SELECT * FROM guest WHERE guest_id=?";
        $guest = DB::select($sql, array($participantId))->first();
        if (!is_null($guest)) {
            return "guest";
        }
        return "none";
    }
    
    /**
     * Returns public information about provided participant
     * 
     * @param type $participantId
     * @return type
     */
    public static function getPublicParticipantInfo($participantId) {
        $guest = DB::table('view_participant')
            ->where('participant_id', $participantId)
            ->select('user_id', 'participant_id', 'participant_type', 
                    'name', 'street', 'neighborhood', 'pic_small',
                    'pic_large', 'description')
            ->first();
        return $guest;
    }
    
    /**
     * Returns restricted (friends-only) info about provided participant.
     * Does not include private info they are not sharing with friends
     * 
     * @param type $participantId
     * @return type
     */
    public static function getRestrictedParticipantInfo($participantId) {
        $guest = DB::table('view_participant')
            ->where('participant_id', $participantId)
            ->first();
        return $guest;
    }
    
    /**
     * Gets a sorted list of all people physically near provided person.  Does
     * not include guests or groups.  Not filtered based on friendship.
     * !Could easily be modified to include groups
     * 
     * @param type $personId
     * @return type
     */
    public static function getNearbyParticipants($personId) {
        //also need to exclude current friends
        $result =  DB::select('SELECT V.user_id, V.participant_id, V.name, ' .
                "V.street, V.neighborhood, V.pic_small, V.participant_type, V.pic_large, V.description, " .
                'SQRT(POW(P.longitude-F.longitude,2)+POW(P.latitude-F.latitude,2)) AS distance ' .
                'FROM member AS P, member AS F INNER JOIN view_participant V on F.member_id = V.participant_id ' .
                "WHERE P.member_id = ? AND F.member_id <> ? AND V.participant_type <> 'group'" .
                'ORDER BY distance LIMIT 100 '
                , array($personId, $personId));
        foreach($result as $row) {
            if ($row->pic_small) {
                $row->profilePicThumbUrl = Config::get('constants.profilePicUrlPath') . 
                        $row->pic_small;
            } else {
                $row->profilePicThumbUrl = Config::get('constants.genericProfilePicUrl');
            }
        }
        return $result;

    }
    
    /**
     * Accepts a string of keywords and returns a list of people and groups
     * that match the string.  Guests are excuded.
     * 
     * @param type $searchString
     * @return type
     */
    public static function searchParticipants($searchString) {
        $searchArray = explode(" ", $searchString);
        //$sql  = "SELECT member.member_id, person.first_name, person.last_name, ";
        //$sql .= "member.street, member.neighborhood, member.city, member.pic_small ";
        //$sql .= "FROM member INNER JOIN person on member.member_id = person.person_id ";
        //$sql .= "WHERE ";
        $result1 = DB::table('view_participant')
                ->join('person', 'person.person_id', '=', 'view_participant.participant_id')
                ->whereIn('person.first_name', $searchArray)
                ->whereIn('person.last_name', $searchArray)
                ->get();
        foreach($result1 as $row) {
            $row->type = "person";
        }
        /*
        $result2 = DB::table('member')
                ->join('person', 'person.person_id', '=', 'member.member_id')
                ->whereIn('person.first_name', $searchArray)
                ->orWhere(function($query)
                {
                    $query->whereIn('person.last_name', $searchArray);
                })
                ->get();
         * 
         */
        //group search needs to be more flexible
        $result3 = DB::table('view_participant')
                ->join('group', 'group.group_id', '=', 'view_participant.participant_id')
                ->where('group.title', "=", $searchString)
                ->get();
        foreach($result3 as $row) {
            $row->type = "group";
        }
        $result = array_merge($result1, $result3);
        foreach($result as $row) {
            if ($row->pic_small) {
                $row->profilePicThumbUrl = Config::get('constants.profilePicUrlPath') . 
                        $row->pic_small;
            } else {
                $row->profilePicThumbUrl = Config::get('constants.genericProfilePicUrl');
            }
        }
        return $result;
    }
    
    public static function getFriendsOfFriends($personId) {
        
    }


    

    
}

