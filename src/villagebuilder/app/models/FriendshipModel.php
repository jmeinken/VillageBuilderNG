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
            return "requesting";
        } elseif (!$friendshipForward && $friendshipBackward) {
            return "request received";
        } elseif ($guestFriendshipForward || $guestFriendshipBackward) {
            return "guest";
        } else {
            return "none";
        }
    }
    
    public static function getFriendships($personId) {
        $reciprocated = array();
        $requesting = array();
        $requestReceived = array();
        $guest = array();
        $sql = "SELECT friend_id FROM friendship WHERE person_id=? AND friend_id IN " . 
                "(SELECT person_id FROM friendship WHERE friend_id=?)";
        $results = DB::select($sql, array($personId, $personId));
        foreach ($results as $row) {
            $reciprocated[] = $row->friend_id;
        }
        $sql = "SELECT friend_id FROM friendship WHERE person_id=? AND friend_id NOT IN " . 
                "(SELECT person_id FROM friendship WHERE friend_id=?)";
        $results = DB::select($sql, array($personId, $personId));
        foreach ($results as $row) {
            $requesting[] = $row->friend_id;
        }
        $sql = "SELECT person_id FROM friendship WHERE friend_id=? AND person_id NOT IN " . 
                "(SELECT friend_id FROM friendship WHERE person_id=?)";
        $results = DB::select($sql, array($personId, $personId));
        foreach ($results as $row) {
            $requestReceived[] = $row->person_id;
        }
        $sql = "SELECT guest_id FROM guest_friendship WHERE person_id=?";
        $results = DB::select($sql, array($personId));
        foreach ($results as $row) {
            $guest[] = $row->guest_id;
        }
        return array("reciprocated" => $reciprocated,
                "requesting" => $requesting,
                "requestReceived" => $requestReceived,
                "guest" => $guest,
        );
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
        $sql = "SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM friendship INNER JOIN view_participant V ON friendship.friend_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE friendship.person_id = ? " .
                "AND friendship.friend_id IN (SELECT person_id FROM friendship F " .
                "WHERE F.friend_id = ?) ";
        $result1 = DB::select($sql, array($personId, $personId));
        $sql = "SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM friendship INNER JOIN view_participant V ON friendship.friend_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
                "WHERE friendship.person_id = ? " .
                "AND friendship.friend_id NOT IN (SELECT person_id FROM friendship F " .
                "WHERE F.friend_id = ?) ";
        $result2 = DB::select($sql, array($personId, $personId));
        $sql = "SELECT V.user_id, V.participant_id, V.participant_type,  " .
                "V.name, V.street, V.neighborhood, V.pic_small, V.pic_large, V.description " . 
                "FROM friendship INNER JOIN view_participant V ON friendship.person_id = V.participant_id " .
                "INNER JOIN member ON member.member_id = V.participant_id " .
        "WHERE friendship.friend_id = ? " .
                "AND friendship.person_id NOT IN (SELECT friend_id FROM friendship F " .
                    "WHERE F.person_id = ?) ";
        $result3 = DB::select($sql, array($personId, $personId));
        $result = array_merge($result1, $result2, $result3);
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
    
    
        
    
    
}