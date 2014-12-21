<?php

class GroupMemberModel {
    
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
    
    public static function approveMembership($personId, $groupId) {
        return DB::table('group_member')->update(
            array(
                   'approved' => 1
            ))
            ->where('person_id', $personId)
            ->where('group_id', $groupId);
    }
    
    public static function alterMembership($personId, $groupId, $watchingOnly) {
        return DB::table('group_member')->update(
            array(
                   'watchingOnly' => $watchingOnly
            ))
            ->where('person_id', $personId)
            ->where('group_id', $groupId);
    }
    
    public static function getMemberships($personId) {
        $result1 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "'member' AS `relationship_type` " . 
                "FROM group_member INNER JOIN `group` ON group_member.group_id = `group`.group_id " .
                "INNER JOIN member ON member.member_id = `group`.group_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($personId));
        $result2 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "'watcher' AS `relationship_type` " . 
                "FROM group_member INNER JOIN `group` ON group_member.group_id = `group`.group_id " .
                "INNER JOIN member ON member.member_id = `group`.group_id " .
                "WHERE group_member.person_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($personId));
        $result3 = DB::select("SELECT `group`.group_id, `group`.title, member.pic_small, " .
                "'unconfirmed' AS `relationship_type` " . 
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


    
    public static function getMembers($groupId) {
        $result1 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "'member' AS `relationship_type` " . 
                "FROM group_member INNER JOIN person ON group_member.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 0 " . 
                "AND group_member.approved = 1", array($groupId));
        $result2 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "'watcher' AS `relationship_type` " . 
                "FROM group_member INNER JOIN person ON group_member.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
                "WHERE group_member.group_id = ? " . 
                "AND group_member.watching_only = 1 " . 
                "", array($groupId));
        $result3 = DB::select("SELECT person.person_id, person.first_name, person.last_name, member.pic_small, " .
                "'unconfirmed' AS `relationship_type` " . 
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
    
    public static function getFriendCount($personId) {
        return DB::table('friendship')
                ->where('person_id', $personId)
                ->count();
    }
    
    public static function getFriendRequests($personId) {
        return DB::select('SELECT person_id'.
                ' FROM friendships'.
                ' WHERE friend_id = ?'.
                ' AND person_id NOT IN (SELECT friend_id'.
                       ' FROM friendships'.
                       ' WHERE person_id = ?)'
                , array($personId, $personId));
    }
    
    public static function getNearbyPeople($personId) {
        //also need to exclude current friends
        $result =  DB::select('SELECT F.member_id, person.first_name, person.last_name, ' .
                'F.street, F.neighborhood, F.city, F.pic_small, ' .
                'SQRT(POW(P.longitude-F.longitude,2)+POW(P.latitude-F.latitude,2)) AS distance ' .
                'FROM member AS P, member AS F INNER JOIN person on F.member_id = person.person_id ' .
                'WHERE P.member_id = ? AND F.member_id <> ? ' .
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
    
    public static function searchParticipants($searchString) {
        $searchArray = explode(" ", $searchString);
        //$sql  = "SELECT member.member_id, person.first_name, person.last_name, ";
        //$sql .= "member.street, member.neighborhood, member.city, member.pic_small ";
        //$sql .= "FROM member INNER JOIN person on member.member_id = person.person_id ";
        //$sql .= "WHERE ";
        $result1 = DB::table('member')
                ->join('person', 'person.person_id', '=', 'member.member_id')
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
        $result3 = DB::table('member')
                ->join('group', 'group.group_id', '=', 'member.member_id')
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