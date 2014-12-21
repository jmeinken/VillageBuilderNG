<?php

class GroupModel {
    
    public static function groupExists($field, $value) {
        $group = DB::table('group')
            ->where($field, '=', $value)
            ->first();
        return (is_null($group) ? false : true);
    }
    
    public static function deleteGroup($participantId) {
        return DB::table('participant')->where('participant_id', $participantId)->delete();
    }
    
    //all values except code should be set in Input
    public static function createGroup($userId) {
        $personId = DB::table('participant')->where('user_id', $userId)->pluck('participant_id');
        try {
            DB::transaction(function() use ($personId, $userId) {
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
               DB::table('group')->insert(
                   array(
                       'group_id' => $participantId, 
                       'title' => Input::get('title'),
                       'description' => Input::get('description'),
                       'email' => Input::get('email')
                   )
               );
               DB::table('group_member')->insert(
                    array(
                        'person_id' => $personId,
                        'group_id' => $participantId,
                        'watching_only' => 0,
                        'approved' => 1
                    )
               );
           });
           return true;
        } catch(Exception $e) {
           return $e;
        }
    }
    
    
    //all values except code should be set in Input
    public static function updateGroup() {
        try {
            DB::transaction(function() {
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
                DB::table('group')
                    ->where('group_id', Input::get('member_id'))
                    ->update(array(
                        'title' => Input::get('title'),
                        'description' => Input::get('description'),
                        'email' => Input::get('email')
                     ));
           });
           return true;
        } catch(Exception $e) {
           return $e;
        }
    }

}