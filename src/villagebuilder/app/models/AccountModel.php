<?php

class AccountModel {
    
    
    public static function accountExists($field, $value) {
        $user = DB::table('users')
            ->where($field, '=', $value)
            ->first();
        return (is_null($user) ? false : true);
    }
    
    
    //all values except code should be set in Input
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
               $memberId = DB::table('member')->insertGetId(
                   array(
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
                       'pic_small' => Input::get('pic_small'), 
                       'user_id' => $userId 
                   )
               );
               DB::table('person')->insert(
                   array(
                       'member_id' => $memberId, 
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
    
    
    //all values except code should be set in Input
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
                        'pic_small' => Input::get('pic_small'), 
                        'user_id' => Input::get('user_id')
                     ));
                DB::table('person')
                    ->where('member_id', Input::get('member_id'))
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