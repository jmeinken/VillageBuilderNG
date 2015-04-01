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
                       'email' => $email, 
                       'active' => 1
                   )
               );
               $participantId = DB::table('participant')->insertGetId(
                   array(
                       'user_id' => $userId,
                       'participant_type' => 'guest'
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
    
    public static function createGuestRelationship($personId, $guestId) {
        return DB::table('guest_friendship')->insert(
                array(
                    'person_id' => $personId,
                    'guest_id' => $guestId
                )
        );
    }
    

    
    


    
    
}