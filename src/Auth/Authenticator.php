<?php

namespace AccessManager\Radius\Auth;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use AccessManager\Constants\Subscription;
use AccessManager\Radius\Helpers\Radius;

/**
 * This class takes care of authentication part of the authorization command.
 *
 * Class Authenticator
 * @package AccessManager\Radius
 */
class Authenticator
{
    /**
     * @var AccountSubscription
     */
    protected $subscription;

    /**
     * Authenticator constructor.
     * @param AccountSubscription $subscription
     */
    public function __construct( AccountSubscription $subscription )
    {
        $this->subscription = $subscription;
    }

    /**
     * Check if an valid subscription model is provided.
     *
     * @return $this
     */
    public function checkIsValidSubscription()
    {
        if( $this->subscription == null)
            Radius::reject("Invalid Subscription.");
        return $this;
    }

    /**
     * Check if account subscription status is active.
     *
     * @return $this
     */
    public function checkActivationStatus()
    {
        if( $this->subscription->status !== Subscription::STATUS_ACTIVE )
            Radius::reject("subscription in-active.");
        return $this;
    }

    /**
     * Check if subscription have services assigned
     * e.g. is recharged if prepaid, and plan assigned if free.
     *
     * @return $this
     */
    public function checkHaveServicesAssigned()
    {
        if( $this->subscription->name == null )
            Radius::reject("No Services Assigned.");
        return $this;
    }

    /**
     * Check if subscription has been expired.
     *
     * @return $this
     */
    public function checkIfSubscriptionExpired()
    {
        if( $this->subscription->hasExpired() )
            Radius::reject("Subscription expired, please renew.");

        return $this;
    }

    /**
     * In case of subscription services with limited data/time quotas,
     * check if quota is available.
     *
     * @return $this
     */
    public function checkIfQuotaIsAvailable()
    {
        if( $this->subscription->quotaExhausted())
            Radius::reject("ran out of quota, refill.");
        return $this;
    }
}