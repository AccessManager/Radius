<?php

namespace AM3\Radius\Accounting;


use AccessManager\Radius\AccountSubscriptionWrapper;

class CoA
{
    /**
     * @var InterimUpdate
     */
    protected $interimUpdate;
    /**
     * @var AccountSubscriptionWrapper
     */
    protected $subscription;

    public function invoke()
    {

    }

    /**
     * CoA constructor.
     * @param InterimUpdate $interimUpdate
     * @param AccountSubscriptionWrapper $subscription
     */
    public function __construct( InterimUpdate $interimUpdate, AccountSubscriptionWrapper $subscription )
    {
        $this->interimUpdate = $interimUpdate;
        $this->subscription = $subscription;
    }
}