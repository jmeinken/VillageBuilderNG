<?php


class UserModel {
    
      
    /**
     * Get the type of individual associated with an account.  Possible outputs
     * are "person", "guest" or "none"
     * 
     * @param type $email
     * @return string
     */
    public static function getUserTypeByEmail($email) {
        //return person, guest, none
        $sql = "SELECT * FROM users "
            . "INNER JOIN participant ON users.id = participant.user_id "
            . "INNER JOIN member ON member.member_id = participant.participant_id "
            . "WHERE users.email=?";
        $person = DB::select($sql, array($email));
        if ($person) {
            return "person";
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
     * returns list of participant_ids and types for the current user
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
     * ?? How does this handle users with multiple participants (i.e. users
     * who have groups)?
     * 
     * @param type $email
     * @return type
     */
    public static function getParticipantIdByEmail($email) {
        $sql = "SELECT participant.participant_id FROM users "
            . "INNER JOIN participant ON users.id = participant.user_id "
            . "WHERE users.email=?";
        $result =  DB::select($sql, array($email));
        return $result->participant_id;
    }
    
    public static function getParticipantIdByUserId($userId) {
        $sql = "SELECT participant.participant_id FROM users "
            . "INNER JOIN participant ON users.id = participant.user_id "
            . "WHERE users.id=?";
        $result =  DB::select($sql, array($userId));
        return $result->participant_id;
    }
    

    
}

