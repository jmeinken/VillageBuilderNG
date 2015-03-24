<?php

class GuestModel {
    
    /**
     * Create a new guest.  All guest info is passed as arguements.     * 
     * 
     * @param type $email
     * @param type $firstName
     * @param type $lastName
     * @param type $code
     * @param type $requesterId
     * @return boolean|\Exception
     */
    public static function createGuest($email, $firstName, $lastName, $code, $requesterId) {
        try {
            DB::transaction(function() use ($email, $firstName, $lastName, $code, $requesterId) {
               $userId = DB::table('users')->insertGetId(
                   array(
                       'email' => Input::get('email'), 
                       'active' => 1
                   )
               );
               $participantId = DB::table('participant')->insertGetId(
                   array(
                       'user_id' => $userId,
                   )
               );
               DB::table('guest')->insert(
                   array(
                       'guest_id' => $participantId, 
                       'first_name' => $firstName,
                       'last_name' => $lastName,
                       'code' => $code
                   )
               );
               DB::table('guest_friendship')->insert(
                    array(
                        'person_id' => $requesterId,
                        'guest_id' => $participantId
                    )
               );
           });
           return true;
        } catch(Exception $e) {
           return $e;
        }
    }
    
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
    
    /**
     * Returns all data from users, participant and guest tables for guest.
     * 
     * @param type $field
     * @param type $value
     * @return type
     */
    public static function getGuest($field, $value) {
        $guest = DB::table('users')
            ->join('participant', 'users.id', '=', 'participant.user_id')
            ->join('guest', 'participant.participant_id', '=', 'guest.guest_id')
            ->where($field, '=', $value)
            ->first();
        return $guest;
    }
    
    public static function deletePersonGuestFriendship($personId, $guestId) {
        
    }
    
    public static function deleteGuest($guestId) {
        
    }
    
    
}