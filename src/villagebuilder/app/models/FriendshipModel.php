<?php

class FriendshipModel {
    
    public static function createFriendship($personId, $friendId) {
        return DB::table('friendship')->insert(
            array(
                   'person_id' => $personId,
                   'friend_id' => $friendId
               )
        );
    }
    
    public static function deleteFriendship($personId, $friendId) {
        return DB::table('friendship')->where('person_id', $personId)
                ->where('friend_id', $friendId)->delete();
    }
    
    public static function getFriends($personId) {
        //make 3 sets: reciprocated, requested, unconfirmed
        $sql = "SELECT person.person_id, member.pic_small, person.first_name, person.last_name, " .
                "member.street, member.neighborhood, member.city, " .
                "'reciprocated' AS `relationship_type`, 'person' as `type` " .
        "FROM friendship INNER JOIN person ON friendship.friend_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
        "WHERE friendship.person_id = ? " .
                "AND friendship.friend_id IN (SELECT person_id FROM friendship F " .
                    "WHERE F.friend_id = ?) ";
        $result1 = DB::select($sql, array($personId, $personId));
        $sql = "SELECT person.person_id, member.pic_small, person.first_name, person.last_name, " .
                "member.street, member.neighborhood, member.city, " .
                "'unconfirmed' AS `relationship_type`, 'person' as `type` " .
        "FROM friendship INNER JOIN person ON friendship.friend_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
        "WHERE friendship.person_id = ? " .
                "AND friendship.friend_id NOT IN (SELECT person_id FROM friendship F " .
                    "WHERE F.friend_id = ?) ";
        $result2 = DB::select($sql, array($personId, $personId));
        $sql = "SELECT person.person_id, member.pic_small, person.first_name, person.last_name, " .
                "member.street, member.neighborhood, member.city, " .
                "'requested' AS `relationship_type`, 'person' as `type` " .
        "FROM friendship INNER JOIN person ON friendship.person_id = person.person_id " .
                "INNER JOIN member ON member.member_id = person.person_id " .
        "WHERE friendship.friend_id = ? " .
                "AND friendship.person_id NOT IN (SELECT friend_id FROM friendship F " .
                    "WHERE F.person_id = ?) ";
        $result3 = DB::select($sql, array($personId, $personId));
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
                "F.street, F.neighborhood, F.city, F.pic_small, 'person' AS `type`, " .
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