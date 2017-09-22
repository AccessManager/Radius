<?php

namespace AM3\Radius\Accounting;


use AccessManager\Radius\AccountSubscriptionWrapper;

class Accountant
{
    /**
     * @var AccountSubscriptionWrapper
     */
    protected $subscription;

    /**
     * @var InterimUpdate
     */
    protected $interimUpdate;

    /**
     * @return bool
     */
    public function isCountable()
    {
        return $this->subscription->isLimited() && $this->subscription->limitExhausted();
    }

    /**
     * @return bool
     */
    public function isNotCountable()
    {
        return ! $this->isCountable();
    }

    /**
     * @return bool
     */
    public function timeCountable()
    {
        return $this->subscription->haveTimeLimit();
    }

    /**
     * @return bool
     */
    public function dataCountable()
    {
        return $this->subscription->haveDataLimit();
    }

    /**
     * @return int
     */
    public function newTimeBalance()
    {
        return $this->subscription->timeBalance() - $this->interimUpdate->acctSessionTime;
    }

    /**
     * @return int
     */
    public function newDataBalance()
    {
        return $this->subscription->dataBalance() - $this->interimUpdate->acctSessionData;
    }

    /**
     * @return bool
     */
    public function quotaExceeded()
    {
        if(
            ( $this->timeCountable() && $this->newTimeBalance() <= 0 )
            || ( $this->dataCountable() && $this->newDataBalance() <= 0 )
        )
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function CoARequired()
    {
        if( $this->subscription->isUnlimited() || $this->subscription->aqNotAllowed() )
            return false;

        return $this->quotaExceeded();
    }

    public function count()
    {
        $balance = [];
        if( $this->timeCountable() )
        {
            $balance['time_balance'] = $this->newTimeBalance();
        }
        if( $this->dataCountable() )
        {
            $balance['data_balance'] = $this->newDataBalance();
        }
        $originalSubscription = $this->subscription->getOriginal();
        $originalSubscription->services()->update($balance);
    }

    /**
     * Accountant constructor.
     * @param AccountSubscriptionWrapper $subscription
     * @param InterimUpdate $interimUpdate
     */
    public function __construct( AccountSubscriptionWrapper $subscription, InterimUpdate $interimUpdate )
    {
        $this->subscription = $subscription;
        $this->interimUpdate = $interimUpdate;
    }

}