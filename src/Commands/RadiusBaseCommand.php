<?php

namespace AccessManager\Radius\Commands;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use Illuminate\Console\Command;
use AccessManager\Hotspot\Models\HotspotSubscription;

class RadiusBaseCommand extends Command
{
    protected function getSubscriptionFromUsername( $username )
    {
        if( $this->_usernameIsMobileNumber($username))
        {
            $subscription = HotspotSubscription::where('username', $username)->firstOrFail();

        } else {
            $subscription = AccountSubscription::where('username', $username)->firstOrFail();
        }

        return $subscription;

    }

    private function _usernameIsMobileNumber( $username )
    {
        return preg_match( "/^[6-9][0-9]{9}$/", $username);
    }

}