<?php

namespace AccessManager\Radius;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;

class AccountSubscriptionWrapper
{
    protected $subscription;

    public function isLimited()
    {
        return $this->subscription->limits !== null;
    }

    public function isUnlimited()
    {
        return ! $this->isLimited();
    }

    public function limitExhausted()
    {

        if( $this->isLimited() )
        {
            return $this->services->exhausted;
        }
        return false;
    }

    public function haveTimeLimit()
    {
        return $this->isLimited() && $this->subscription->limits->time_limit !== null;
    }

    public function haveDataLimit()
    {
        return $this->isLimited() && $this->subscription->limits->data_limit !== null;
    }

    public function aqNotAllowed()
    {
        return $this->subscription->aqPolicy == null;
    }

    public function timeBalance()
    {
        if( $this->subscription->limits )
        {
            return $this->subscription->services->time_balance;
        }
        return 0;
    }

    public function dataBalance()
    {
        if( $this->subscription->limits )
        {
            return $this->subscription->services->data_balance;
        }
        return 0;
    }

    public function getOriginal()
    {
        return $this->subscription;
    }

    public function __get($name)
    {
        return $this->subscription->$name;
    }

    public function __construct( AccountSubscription $subscription )
    {
        $this->subscription = $subscription;
    }

}