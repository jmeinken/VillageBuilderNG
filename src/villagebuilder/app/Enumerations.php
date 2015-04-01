<?php



//enumeration of algorithms
//call with Algorithms::ALGNAME
abstract class Relationships  {
    const NONE = 'none';
    
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
