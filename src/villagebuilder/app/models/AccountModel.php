<?php

/**
 * An account corresponds to a single set of login credentials on the site.
 * For historic reasons, the table for account info is called 'Users'.
 * 
 * The definition of Account is a little confused.  Some of these methods are
 * for USER accounts, others are specifically for PERSON accounts.
 * 
 */
class AccountModel {
    
    /**
     * Accepts an account ID and returns an array of participant IDs on that 
     * account. An account can be for a single guest, a single person, or a 
     * single person with one-to-many groups.  Guests, persons and groups are 
     * all participants.  
     * 
     * @param int $userId
     * @return int[]
     */
    public static function getParticipantsForUser($userId) {
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
     * Gets information about a PERSON using any unique field (person_id, email,
     * etc.). Returns user_id, person_id, first_name, last_name, street, 
     * neighborhood, city, and pic_small (path).
     * 
     * @param string $field
     * @param string $value
     * @return type
     */
    public static function getAccountBasic($field, $value) {
        $user = DB::table('users')
            ->join('participant', 'users.id', '=', 'participant.user_id')
            ->join('member', 'member.member_id', '=', 'participant.participant_id')
            ->join('person', 'person.person_id', '=', 'member.member_id')
            ->where($field, '=', $value)
            ->select('users.id','person.person_id', 'person.first_name',
                    'person.last_name', 'member.street', 'member.neighborhood',
                    'member.city','member.pic_small')
            ->first();
        if (is_object($user)) {
            $user->type = 'person';
            if ($user->pic_small) {
                $user->profilePicThumbUrl = Config::get('constants.profilePicUrlPath') . 
                        $user->pic_small;
            } else {
                $user->profilePicThumbUrl = Config::get('constants.genericProfilePicUrl');
            }
        }
        return $user;
    }
    
    /**
     * Check if a USER account exists on any unique field (email, etc.).  Field
     * must be contained in users table.
     * 
     * @param type $field
     * @param type $value
     * @return boolean
     */
    public static function accountExists($field, $value) {
        $user = DB::table('users')
            ->where($field, '=', $value)
            ->first();
        return (is_null($user) ? false : true);
    }
    
    /**
     * Deletes a USER account (and any dependent guest, person or groups).
     * 
     * @param int $userId
     * @return boolean
     */
    public static function deleteAccount($userId) {
        return DB::table('users')->where('id', $userId)->delete();
    }
    
    //
    /**
     * Creates a new PERSON account.  All relevant values except code should be 
     * set in input.
     * 
     * @param string $code
     * @return boolean
     */
    public static function createAccount($code) {
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
                       'user_id' => $userId 
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
    public static function updateAccount() {
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