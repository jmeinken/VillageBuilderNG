<?php

/**
 * Handles relationships between people and groups.  Guests can not be members
 * of groups.
 * 
 * There are two flags associated with a group membership: watching_only means
 * a person can view a groups activity but is not an official member; 
 * approved means that the group owner has accepted a person as a member.
 */
class MembershipModel {
    
    /**
     * Creates an unapproved group membership.
     * 
     * @param type $personId
     * @param type $groupId
     * @param type $watchingOnly
     * @return type
     */
    public static function createMembership($personId, $groupId, $watchingOnly) {
        return DB::table('group_member')->insert(
            array(
                   'person_id' => $personId,
                   'group_id' => $groupId,
                   'watching_only' => $watchingOnly,
                   'approved' => 0
               )
        );
    }
    
    public static function checkMembership($personId, $groupId) {
        $result = DB::table('group_member')
                ->where('group_id', $groupId)
                ->where('person_id', $personId)
                ->first(); 
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    public static function deleteMembership($personId, $groupId) {
        return DB::table('group_member')->where('person_id', $personId)
                ->where('group_id', $groupId)->delete();
    }
    
    /**
     * Switches a person's membership status to approved.  Returns false if
     * fails.  If a person is already approved, returns true.
     * 
     * @param type $personId
     * @param type $groupId
     * @return type
     */
    public static function approveMembership($personId, $groupId) {
        return DB::table('group_member')
            ->where('person_id', $personId)
            ->where('group_id', $groupId)
            ->update(array(
                   'approved' => 1
            ));
            
    }
    
    /**
     * Used to modify the watching_only status of a group member.
     * 
     * @param type $personId
     * @param type $groupId
     * @param type $watchingOnly
     * @return type
     */
    public static function alterMembership($personId, $groupId, $watchingOnly) {
        return DB::table('group_member')
            ->where('person_id', $personId)
            ->where('group_id', $groupId)
            ->update(array(
                   'watching_only' => $watchingOnly
            ));
    }
    
    /**
     * Returns group information (id, title, small pic, street, neighborhood,
     * city) for all groups privided person belongs to.  Also specifies
     * relationship_type as "member", "watcher" or "unconfirmed"
     * 
     * @param type $personId
     * @return type
     */
    public static function getMembershipCollection($personId) {
        $result1 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.group_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($personId));
        $result2 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.group_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($personId));
        $result3 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.group_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 0", array($personId));
        $result = array_merge($result1, $result2, $result3);
        return $result;
    }
    
    public static function getMemberships($personId) {
        $member = array();
        $watching = array();
        $membershipRequested = array();
        $sql = "SELECT group_id FROM group_member WHERE person_id=? AND watching_only=0 AND approved=1";
        $results = DB::select($sql, array($personId));
        foreach ($results as $row) {
            $member[] = $row->group_id;
        }
        $sql = "SELECT group_id FROM group_member WHERE person_id=? AND watching_only=1";
        $results = DB::select($sql, array($personId));
        foreach ($results as $row) {
            $watching[] = $row->group_id;
        }
        $sql = "SELECT group_id FROM group_member WHERE person_id=? AND watching_only=0 AND approved=0";
        $results = DB::select($sql, array($personId));
        foreach ($results as $row) {
            $membershipRequested[] = $row->group_id;
        }
        return array("member" => $member,
                "watching" => $watching,
                "membershipRequested" => $membershipRequested,
        );
    }
    
    public static function getMembers($groupId) {
        $member = array();
        $watching = array();
        $membershipRequested = array();
        $sql = "SELECT person_id FROM group_member WHERE group_id=? AND watching_only=0 AND approved=1";
        $results = DB::select($sql, array($groupId));
        foreach ($results as $row) {
            $member[] = $row->person_id;
        }
        $sql = "SELECT person_id FROM group_member WHERE group_id=? AND watching_only=1";
        $results = DB::select($sql, array($groupId));
        foreach ($results as $row) {
            $watching[] = $row->person_id;
        }
        $sql = "SELECT person_id FROM group_member WHERE group_id=? AND watching_only=0 AND approved=0";
        $results = DB::select($sql, array($groupId));
        foreach ($results as $row) {
            $membershipRequested[] = $row->person_id;
        }
        return array("member" => $member,
                "watching" => $watching,
                "membershipRequested" => $membershipRequested,
        );
    }
    
    public static function getOwner($userId) {
        return DB::table('view_participant')
                ->where("user_id", $userId)
                ->where("participant_type", "person")
                ->pluck("participant_id");
    }
    
    public static function getOwnerships($personId, $userId) {
        $owner = array();
        $sql = "SELECT participant_id FROM participant WHERE user_id=? AND participant_id <> ?";
        $results = DB::select($sql, array($userId, $personId));
        foreach ($results as $row) {
            $owner[] = $row->participant_id;
        }
        return array("owner" => $owner,
        );
    }


    /**
     * Returns person information (id, name, pic, street, neighborhood, city)
     * for all people associated with provided group.  Also gives 
     * relationship_type as 'member', 'watcher' or 'unconfirmed'
     * 
     * @param type $groupId
     * @return type
     */
    public static function getMemberCollection($groupId) {
        $result1 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.person_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($groupId));
        $result2 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.person_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($groupId));
        $result3 = DB::select("SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM group_member INNER JOIN view_participant V ON group_member.person_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 0", array($groupId));
        $result = array_merge($result1, $result2, $result3);
        return $result;
    }

    /**
     * For provided group, returns data about people who are either watching
     * or haven't been approved (or both).
     * 
     * @param type $groupId
     * @return type
     */
    public static function getWatchers($groupId) {
        $result = DB::table('group_member')
                ->join('person', 'person.person_id', '=', 'group_member.person_id')
                ->join('member', 'person.person_id', '=', 'member.member_id')
                ->where('group_member.group_id', $groupId)
                ->where(function($query)
                    {
                        $query->where('group_member.watching_only', 1)
                              ->orWhere('group_member.approved', 0);
                    })
                ->get();
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
    

        
    
    
}