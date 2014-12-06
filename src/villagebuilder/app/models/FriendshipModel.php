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
        return DB::table('friendship')
                ->where('person_id', $personId)
                ->get();
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
    
    
}