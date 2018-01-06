<?php

namespace AccessManager\Radius\Accounting;


use AccessManager\Constants\Data;
use AccessManager\Constants\Radius;

class InterimUpdate
{
    private $requiredAttributes     =   [
        'User-Name','Acct-Session-Id','Acct-Unique-Session-Id','Acct-Input-Octets','Acct-Output-Octets',
        'Acct-Input-Gigawords','Acct-Output-Gigawords','Acct-Session-Time','Acct-Status-Type'
    ];
    private $attributes     =   [];

    public static function createFromAttributes( array $accountingAttributes )
    {
        return new self($accountingAttributes);
    }

    public function isNewSession()
    {
        return $this->acctStatusType == Radius::SESSION_START;
    }

    public function __get($name)
    {
        if( array_key_exists($name, $this->attributes) )
            return $this->attributes[$name];

        throw new \Exception("Property: {$name} Not Found.");
    }

    private function _constructAttributes( $attributes )
    {
        $result =   [];
        foreach ($attributes as $key    =>  $value )
        {
            $key    =   str_replace('-','', $key);
            $key = lcfirst($key);
            $result[$key]   =   $value;
        }

        $result['acctInputData']  = ( $result['acctInputGigawords'] == 0 ) ? $result['acctInputOctets'] : ( $result['acctInputGigawords'] * Data::FOUR_GB + $result['acctInputOctets'] );
        $result['acctOutputData']  = ( $result['acctOutputGigawords'] == 0 ) ? $result['acctOutputOctets'] : ( $result['acctOutputGigawords'] * Data::FOUR_GB + $result['acctOutputOctets'] );
        $result['acctSessionData']  = $result['acctInputData']  +   $result['acctOutputData'];

        return $result;
    }

    private function _verifyRequiredAttributes( $attributes )
    {
        foreach($this->requiredAttributes as $requiredAttribute ) {
            if (!array_key_exists($requiredAttribute, $attributes)) {
                throw new \Exception("Insufficient attributes provided. Mising: $requiredAttribute");
            }
        }
    }

    private function __construct( Array $attributes )
    {
        $this->_verifyRequiredAttributes( $attributes );
        $this->attributes   =   $this->_constructAttributes($attributes);
    }
}