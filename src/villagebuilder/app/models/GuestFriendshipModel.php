<?php

class GuestFriendshipModel {
    

    
    /**
     * Create a friendship between a person and a guest.  True on success,
     * false on failure.  If relationship already exists, that is considered a
     * success.
     * 
     * @param type $personId
     * @param type $guestId
     * @return boolean
     */
    public static function createPersonGuestFriendship($personId, $guestId) {
        $existingRelationship = DB::table('guest_friendship')
                ->where('person_id', '=', $personId)
                ->where('guest_id', '=', $guestId)
                ->first();
        if ($existingRelationship) {
            return true;
        }
        return DB::table('guest_friendship')->insert(
                    array(
                        'person_id' => $personId,
                        'guest_id' => $guestId
                    )
               );
    }
    
   
    
    public static function deletePersonGuestFriendship($personId, $guestId) {
        
    }
    
    
    
    
}