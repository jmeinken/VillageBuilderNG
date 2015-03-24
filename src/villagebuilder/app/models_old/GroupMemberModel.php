<?php

/**
 * Handles relationships between people and groups.  Guests can not be members
 * of groups.
 * 
 * There are two flags associated with a group membership: watching_only means
 * a person can view a groups activity but is not an official member; 
 * approved means that the group owner has accepted a person as a member.
 */
class GroupMemberModel {
    
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
        return DB::table('group_member')->update(
            array(
                   'approved' => 1
            ))
            ->where('person_id', $personId)
            ->where('group_id', $groupId);
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
        return DB::table('group_member')->update(
            array(
                   'watchingOnly' => $watchingOnly
            ))
            ->where('person_id', $personId)
            ->where('group_id', $groupId);
    }
    
    /**
     * Returns group information (id, title, small pic, street, neighborhood,
     * city) for all groups privided person belongs to.  Also specifies
     * relationship_type as "member", "watcher" or "unconfirmed"
     * 
     * @param type $personId
     * @return type
     */
    public static function getMemberships($personId) {
        $result1 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'member' AS `relationship_type`, 'group' as `type` " . 
                "FROM group_member INNER JOIN `group` ON group_member.group_id = `group`.group_id " .
                "INNER JOIN member ON member.member_id = `group`.group_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($personId));
        $result2 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'watcher' AS `relationship_type`, 'group' as `type` " . 
                "FROM group_member INNER JOIN `group` ON group_member.group_id = `group`.group_id " .
                "INNER JOIN member ON member.member_id = `group`.group_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($personId));
        $result3 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'unconfirmed' AS `relationship_type`, 'group' as `type` " . 
                "FROM group_member INNER JOIN `group` ON group_member.group_id = `group`.group_id " .
                "INNER JOIN member ON member.member_id = `group`.group_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 0", array($personId));
        $result = array_merge($result1, $result2, $result3);
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
     * Returns person information (id, name, pic, street, neighborhood, city)
     * for all people associated with provided group.  Also gives 
     * relationship_type as 'member', 'watcher' or 'unconfirmed'
     * 
     * @param type $groupId
     * @return type
     */
    public static function getMembers($groupId) {
        $result1 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'member' AS `relationship_type`, 'person' as `type` " . 
                "FROM group_member INNER JOIN person ON group_member.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($groupId));
        $result2 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'watcher' AS `relationship_type`, 'person' as `type` " . 
                "FROM group_member INNER JOIN person ON group_member.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($groupId));
        $result3 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "member.street, member.neighborhood, member.city, " .
                "'unconfirmed' AS `relationship_type`, 'person' as `type` " . 
                "FROM group_member INNER JOIN person ON group_member.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 0", array($groupId));
        $result = array_merge($result1, $result2, $result3);
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