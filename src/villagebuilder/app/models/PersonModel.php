<?php

/**
 * An account corresponds to a single set of login credentials on the site.
 * For historic reasons, the table for account info is called 'Users'.
 * 
 * The definition of Account is a little confused.  Some of these methods are
 * for USER accounts, others are specifically for PERSON accounts.
 * 
 */
class PersonModel {
    

    

    

    

    
    //
    /**
     * Creates a new PERSON account.  All relevant values except code should be 
     * set in input.
     * 
     * @param string $code
     * @return boolean
     */
    public static function createPerson($code) {
        try {
            DB::transaction(function() use ($code) {
               $userId = DB::table('users')->insertGetId(
                   array(
                       'email' => Input::get('email'), 
                       'password' => Hash::make(Input::get('password')),
                       'code' => $code,
                       'active' => 0
                   )
               );
               $participantId = DB::table('participant')->insertGetId(
                   array(
                       'user_id' => $userId,
                       'participant_type' => 'person'
                   )
               );
               DB::table('member')->insert(
                   array(
                       'member_id' => $participantId,
                       'full_address' => Input::get('full_address'), 
                       'address1' => Input::get('address1'), 
                       'address2' => Input::get('address2'), 
                       'city' => Input::get('city'), 
                       'state' => Input::get('state'), 
                       'zip_code' => Input::get('zip_code'), 
                       'latitude' => Input::get('latitude'), 
                       'longitude' => Input::get('longitude'), 
                       'street' => Input::get('street'), 
                       'neighborhood' => Input::get('neighborhood'), 
                       'phone_number' => Input::get('phone_number'), 
                       'phone_type' => Input::get('phone_type'), 
                       'share_email' => Input::get('share_email'), 
                       'share_address' => Input::get('share_address'), 
                       'share_phone' => Input::get('share_phone'), 
                       'pic_large' => Input::get('pic_large'), 
                       'pic_small' => Input::get('pic_small')
                   )
               );
               DB::table('person')->insert(
                   array(
                       'person_id' => $participantId, 
                       'first_name' => Input::get('first_name'),
                       'last_name' => Input::get('last_name')
                   )
               );
           });
           return true;
        } catch(Exception $e) {
           return false;
        }
    }
    
    
    /**
     * Updates an existing PERSON account.  All relevant values should be 
     * set in input.
     * 
     * @return boolean|\Exception
     */
    public static function updatePerson() {
        try {
            DB::transaction(function() {
                DB::table('users')
                    ->where('id', Input::get('user_id'))
                    ->update(array(
                        'email' => Input::get('email')
                     ));
                DB::table('member')
                    ->where('member_id', Input::get('member_id'))
                    ->update(array(
                        'full_address' => Input::get('full_address'), 
                        'address1' => Input::get('address1'), 
                        'address2' => Input::get('address2'), 
                        'city' => Input::get('city'), 
                        'state' => Input::get('state'), 
                        'zip_code' => Input::get('zip_code'), 
                        'latitude' => Input::get('latitude'), 
                        'longitude' => Input::get('longitude'), 
                        'street' => Input::get('street'), 
                        'neighborhood' => Input::get('neighborhood'), 
                        'phone_number' => Input::get('phone_number'), 
                        'phone_type' => Input::get('phone_type'), 
                        'share_email' => Input::get('share_email'), 
                        'share_address' => Input::get('share_address'), 
                        'share_phone' => Input::get('share_phone'), 
                        'pic_large' => Input::get('pic_large'), 
                        'pic_small' => Input::get('pic_small')
                     ));
                DB::table('person')
                    ->where('person_id', Input::get('member_id'))
                    ->update(array(
                        'first_name' => Input::get('first_name'),
                        'last_name' => Input::get('last_name')
                     ));
           });
           return true;
        } catch(Exception $e) {
           return $e;
        }
    }

}