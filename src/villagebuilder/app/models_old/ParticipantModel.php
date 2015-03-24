<?php

class ParticipantModel {
    
    /**
     * For provided participant_id, determines if that participant is a "person",
     * "guest", "group" or "none"
     * 
     * @param type $participantId
     * @return string
     */
    public static function getParticipantTypeById($participantId) {
        //returns guest, person, group, none
        $sql = "SELECT * FROM person WHERE person_id=?";
        $person = DB::select($sql, array($participantId))->first();
        if (!is_null($person)) {
            return "person";
        }
        $sql = "SELECT * FROM group WHERE group_id=?";
        $group = DB::select($sql, array($participantId))->first();
        if (!is_null($group)) {
            return "group";
        }
        $sql = "SELECT * FROM guest WHERE guest_id=?";
        $guest = DB::select($sql, array($participantId))->first();
        if (!is_null($guest)) {
            return "guest";
        }
        return "none";
    }
    
    /**
     * Returns public information about provided participant
     * 
     * @param type $participantId
     * @return type
     */
    public static function getPublicParticipantInfo($participantId) {
        $guest = DB::table('view_participant')
            ->where('participant_id', $participantId)
            ->select('user_id', 'participant_id', 'participant_type', 
                    'name', 'street', 'neighborhood', 'pic_small',
                    'pic_large', 'description')
            ->first();
        return $guest;
    }
    
    /**
     * Returns restricted (friends-only) info about provided participant.
     * Does not include private info they are not sharing with friends
     * 
     * @param type $participantId
     * @return type
     */
    public static function getRestrictedParticipantInfo($participantId) {
        $guest = DB::table('view_participant')
            ->where('participant_id', $participantId)
            ->first();
        return $guest;
    }
    


    

    
}

