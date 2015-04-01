<?php

/*
 * This is a file I added.  It is called from bootstrap/start.php.
 * I believe these classes will be available anywhere in my code.
 * Don't fill this with lots of domain-specific code that shouldn't be
 * loaded every time the app starts.
 */


//enumeration of realtionships
//call with Relationships::MYVAL
abstract class Relationships  {
    const NONE = 'none';
    const SELF = 'self';
    
    //for person-person
    const RECIPROCATED = 'reciprocated';
    const REQUEST_SENT = 'request_sent';
    const REQUEST_RECEIVED = 'request_received';
    
    //for person-group
    const MEMBER = 'member';
    const WATCHER = 'watcher';
    const MEMBERSHIP_REQUESTED = 'membership_requested';
    
    //for person-guest
    const GUEST = 'guest';

    private function __construct()  {}  

    static public function getArray()  {
        $refl1 = new ReflectionClass('Algorithms');
        return $refl1->getConstants();
    }
}


abstract class ParticipantTypes  {
    const NONE = 'none';
    const PERSON = 'person';
    const MEMBER = 'member';
    const GUEST = 'guest';
    const GROUP = 'group';

    private function __construct()  {}  

    static public function getArray()  {
        $refl1 = new ReflectionClass('Algorithms');
        return $refl1->getConstants();
    }
}
