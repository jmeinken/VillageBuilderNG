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
        $result = DB::table('friendship')
                ->join('person', 'person.person_id', '=', 'friendship.friend_id')
                ->join('member', 'person.person_id', '=', 'member.member_id')
                ->where('friendship.person_id', $personId)
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
    
    public static function getFriendsOfFriends($personId) {
        
    }
        
    
    
}