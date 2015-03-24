<?php


class UserModel {
    
      
    /**
     * Get the type of individual associated with an account.  Possible outputs
     * are "person", "guest" or "none"
     * 
     * @param type $email
     * @return string
     */
    public static function getMemberStatusByEmail($email) {
        //return person, guest, none
        $sql = "SELECT * FROM users "
            . "INNER JOIN participant ON users.id = participant.user_id "
            . "INNER JOIN member ON member.member_id = participant.participant_id "
            . "WHERE users.email=?";
        $person = DB::select($sql, array($email));
        if ($person) {
            return "member";
        }
        $sql = "SELECT * FROM users "
            . "INNER JOIN participant ON users.id = participant.user_id "
            . "INNER JOIN guest ON guest.guest_id = participant.participant_id "
            . "WHERE users.email=?";
        $guest = DB::select($sql, array($email));
        if ($guest) {
            return "guest";
        }
        return "none";
    }
    
    /**
     * Check if a USER account exists on any unique field (email, etc.).  Field
     * must be contained in users table.
     * 
     * @param type $field
     * @param type $value
     * @return boolean
     */
    public static function userExists($field, $value) {
        $user = DB::table('users')
            ->where($field, '=', $value)
            ->first();
        return (is_null($user) ? false : true);
    }
    
    /**
     * returns collection of participants for the current user
     * 
     * @param type $email
     * @return string
     */
    public static function getParticipantsForUser($userId) {
        $participants = DB::table('view_participant')
            ->where('user_id', $userId)
            ->select('user_id', 'participant_id', 'participant_type', 
                    'name', 'street', 'neighborhood', 'pic_small',
                    'pic_large', 'description')->get();
        return $participants;
    }
    
    /**
     * Accepts an account ID and returns an array of participant IDs on that 
     * account. An account can be for a single guest, a single person, or a 
     * single person with one-to-many groups.  Guests, persons and groups are 
     * all participants.  
     * 
     * @param int $userId
     * @return int[]
     */
    public static function getParticipantIdsForUser($userId) {
        $participants = DB::table('users')
            ->join('participant', 'users.id', '=', 'participant.user_id')
            ->where('users.id', '=', $userId)
            ->select('participant.participant_id')
            ->get();
        foreach ($participants as $participant) {
            $arr[] = (int) $participant->participant_id;
        }
        return $arr;
    }
    
    
        /**
     * Deletes a USER account (and any dependent guest, person or groups).
     * 
     * @param int $userId
     * @return boolean
     */
    public static function deleteUser($userId) {
        return DB::table('users')->where('id', $userId)->delete();
    }
    

    

    
}

