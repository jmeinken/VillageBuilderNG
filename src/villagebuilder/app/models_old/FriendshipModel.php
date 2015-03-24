<?php

/**
 * This class handles relationships between people.  It does not handle 
 * relationships between people and guests or relationships beween people
 * and groups.
 */
class FriendshipModel {
    
    /**
     * Creates friendship between two people (not guest friendship)
     * 
     * @param type $personId
     * @param type $friendId
     * @return type
     */
    public static function createFriendship($personId, $friendId) {
        return DB::table('friendship')->insert(
            array(
                   'person_id' => $personId,
                   'friend_id' => $friendId
               )
        );
    }
    
    /**
     * Delete a friendship (not guest friendship).  Returns true whether 
     * relationship existed or not.
     * 
     * @param type $personId
     * @param type $friendId
     * @return type
     */
    public static function deleteFriendship($personId, $friendId) {
        return DB::table('friendship')->where('person_id', $personId)
                ->where('friend_id', $friendId)->delete();
    }
    
    /**
     * Return values:
     * 'reciprocated'
     * 'unconfirmed' - person has requested but no response
     * 'requested' - friend has requested but person has not responded
     * 'guest' - friendship exists but one of the two parties is a guest
     * 'none' - no relationship exists
     * 
     * @param type $personId
     * @param type $friendId
     * @return string
     */
    public static function getFriendshipType($personId, $friendId) {
        //return requested, unconfirmed, reciprocated, guest, none
        $sql = "SELECT person_id, friend_id FROM friendship WHERE person_id=? and friend_id=?";
        $friendshipForward = DB::select($sql, array($personId, $friendId));
        $friendshipBackward = DB::select($sql, array($friendId, $personId));
        $sql = "SELECT person_id, guest_id FROM guest_friendship WHERE person_id=? and guest_id=?";
        $guestFriendshipForward = DB::select($sql, array($personId, $friendId));
        $guestFriendshipBackward = DB::select($sql, array($friendId, $personId));
        if ($friendshipForward && friendshipBackward) {
            return "reciprocated";
        } elseif ($friendshipForward && !$friendshipBackward) {
            return "unconfirmed";
        } elseif (!$friendshipForward && $friendshipBackward) {
            return "requested";
        } elseif ($guestFriendshipForward || $guestFriendshipBackward) {
            return "guest";
        } else {
            return "none";
        }
    }
    
    /**
     * Returns array of friends for person with basic information.
     * Relationship type can be 'reciprocated', 'unconfirmed' or 'requested'.
     * 
     * @param type $personId
     * @return type
     */
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
    
    /**
     * Returns count of friends (includes unconfirmed, excludes guest friendships)
     * 
     * @param type $personId
     * @return type
     */
    public static function getFriendCount($personId) {
        return DB::table('friendship')
                ->where('person_id', $personId)
                ->count();
    }
    
    /**
     * Gets person_id for all people who have requested to provided person
     * but have not received a response.
     * 
     * @param type $personId
     * @return type
     */
    public static function getFriendRequests($personId) {
        return DB::select('SELECT person_id'.
                ' FROM friendships'.
                ' WHERE friend_id = ?'.
                ' AND person_id NOT IN (SELECT friend_id'.
                       ' FROM friendships'.
                       ' WHERE person_id = ?)'
                , array($personId, $personId));
    }
    
    /**
     * Gets a sorted list of all people physically near provided person.  Does
     * not include guests or groups.  Not filtered based on friendship.
     * 
     * @param type $personId
     * @return type
     */
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