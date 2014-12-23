<?php

class GuestModel {
    
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
    
    public static function createPersonGuestFriendship($personId, $guestId) {
        return DB::table('guest_friendship')->insert(
                    array(
                        'person_id' => $requesterId,
                        'guest_id' => $participantId
                    )
               );
    }
    
    public static function getGuestByEmail($guestId) {
        
    }
    
    public static function deletePersonGuestFriendship($personId, $guestId) {
        
    }
    
    public static function deleteGuest($guestId) {
        
    }
    
    
}