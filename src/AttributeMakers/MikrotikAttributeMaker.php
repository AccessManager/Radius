<?php

namespace AccessManager\Radius\AttributeMakers;

use AccessManager\Constants\Data;
use AccessManager\Radius\Helpers\Radius;

class MikrotikAttributeMaker implements AttributeMakerInterface
{
    /**
     * @var
     */
    protected $username;

    protected $checks;

    protected $replies;

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function makePassword($password)
    {
        $this->_addCheck([
            'Cleartext-Password'    =>      $password,
        ]);

        return $this;
    }

    public function makeBandwidthPolicy($policy)
    {
        $this->_addReply([
            'Mikrotik-Rate-Limit'       =>      $this->_mikrotikRateLimit($policy->toArray()),
        ]);

        return $this;
    }

    public function makeDataLimit( $bytes )
    {
        if( $bytes > Data::FOUR_GB ) {
            $this->_addReply([
                'Mikrotik-Total-Limit-Gigawords'       =>       intval($bytes / Data::FOUR_GB),
            ]);
            $bytes = bcmod($bytes, Data::FOUR_GB);
        }
        $this->_addReply([
            'Mikrotik-Total-Limit'      =>      $bytes,
        ]);

        return $this;
    }

    public function makeTimeLimit( $seconds )
    {
        $this->_addReply([
            'Session-Timeout'       =>      $seconds,
        ]);

        return $this;
    }

    public function makeInterimInterval( $seconds )
    {
        $this->_addReply([
            'Acct-Interim-Interval'     =>      $seconds,
        ]);

        return $this;
    }

    public function makeSimultaneousSessions( $count )
    {
        $this->_addReply([
            'Simultaneous-Use'      =>      $count,
        ]);

        return $this;
    }

    public function makeIdleTimeout( $seconds )
    {
        $this->_addReply([
            'Idle-Timeout'      =>      $seconds,
        ]);

        return $this;
    }

    public function makeExpiration( $expiration )
    {
        $this->_addCheck([
            'Expiration'    =>  $expiration,
        ]);

        return $this;
    }

    public function makeFramedIp( $ip )
    {
        $this->_addReply([
            'Framed-IP-Address'     =>  $ip,
        ]);

        return $this;
    }

    public function makeFramedRoute( $subnet, $framedIp = '0.0.0.0' )
    {
        $this->_addReply([
            'Framed-Route'      =>  "{$subnet} {$framedIp} 1",
        ]);

        return $this;
    }

    public function getChecks()
    {
        return $this->checks;
    }

    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param array $check
     */
    private function _addCheck( array $check )
    {
        foreach( $check as $attribute => $value )
        {
            $this->checks[] =     [
                'username'      =>      $this->username,
                'attribute'     =>      $attribute,
                'op'            =>      ':=',
                'value'         =>      $value
            ];
        }
    }

    /**
     * @param array $reply
     */
    private function _addReply( array $reply )
    {
        foreach( $reply as $attribute => $value )
        {
            $this->replies[] =     [
                'username'      =>      $this->username,
                'attribute'     =>      $attribute,
                'op'            =>      ':=',
                'value'         =>      $value
            ];
        }
    }

    private function _mikrotikRateLimit( array $v, $prefix = NULL)
    {
        return      "{$v[$prefix.'max_up']}{$v[$prefix.'max_up_unit'][0]}/".
                    "{$v[$prefix.'max_down']}{$v[$prefix.'max_down_unit'][0]} ".
                    "{$v[$prefix.'max_up']}{$v[$prefix.'max_up_unit'][0]}/".
                    "{$v[$prefix.'max_down']}{$v[$prefix.'max_down_unit'][0]} ".
                    "{$v[$prefix.'max_up']}{$v[$prefix.'max_up_unit'][0]}/".
                    "{$v[$prefix.'max_down']}{$v[$prefix.'max_down_unit'][0]} ".
                    "1/1 1 ".
                    "{$v[$prefix.'min_up']}{$v[$prefix.'min_up_unit'][0]}/".
                    "{$v[$prefix.'min_down']}{$v[$prefix.'min_down_unit'][0]}";
    }
}