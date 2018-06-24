<?php

namespace AccessManager\Radius;


use AccessManager\Radius\AttributeMakers\AttributeMakerInterface;

/**
 * This class creates array of applicable radius attributes for radreply table.
 *
 * Class ReplyMaker
 * @package AccessManager\Radius
 */
class ReplyMaker
{
    /**
     * @var AccountSubscriptionWrapper
     */
    protected $subscription;

    /**
     * @var AttributeMakerInterface
     */
    protected $attributesMaker;

    /**
     * Check and add bandwidth policy to the session as per the plan
     *
     * @return $this
     */
    public function addBandwidthPolicy()
    {
        if( $this->subscription->isLimited() && $this->subscription->limitExhausted() )
        {
            $this->attributesMaker->makeBandwidthPolicy( $this->subscription->aqPolicy );
        } else {
            $this->attributesMaker->makeBandwidthPolicy( $this->subscription->primaryPolicy );
        }
        return $this;
    }


    /**
     * Check and add time limit to the session as per the plan
     *
     * @param int $sessionTime
     * @return $this
     */
    public function addTimeLimit( $sessionTime = 0 )
    {
        if( $this->subscription->haveTimeLimit() && $this->subscription->aqNotAllowed() )
        {
            $this->attributesMaker->makeTimeLimit($this->subscription->timeBalance() + $sessionTime);
        } else {
            $this->attributesMaker->makeTimeLimit(0);
        }
        return $this;
    }

    /**
     * Check and add data limit to the session as per the plan
     *
     * @param int $sessionData
     * @return $this
     */
    public function addDataLimit( $sessionData = 0 )
    {
        if( $this->subscription->haveDataLimit() && $this->subscription->aqNotAllowed() )
        {
            $this->attributesMaker->makeDataLimit($this->subscription->dataBalance() + $sessionData );
        } else {
            $this->attributesMaker->makeDataLimit(0);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addInterimInterval()
    {
        $this->attributesMaker->makeInterimInterval( $this->subscription->interim_updates );

        return $this;
    }

    /**
     * @return $this
     */
    public function addIdleTimeout()
    {
        //TODO: Implement IdleTimeout.
        return $this;
    }

    /**
     * @return $this
     */
    public function addFramedIp()
    {
        if( $this->subscription->haveFramedIp() )
        {
            $this->attributesMaker->makeFramedIp( $this->subscription->getFramedIpAddress() );
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addFramedRoute()
    {
        if( $this->subscription->haveFramedRoute() )
            $this->attributesMaker->makeFramedRoute($this->subscription->getFramedRoute());
        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->attributesMaker->getReplies();
    }

    /**
     * ReplyMaker constructor.
     * @param AccountSubscriptionWrapper $subscription
     * @param AttributeMakerInterface $attributeMaker
     */
    public function __construct( AccountSubscriptionWrapper $subscription, AttributeMakerInterface $attributeMaker )
    {
        $this->subscription = $subscription;
        $this->attributesMaker = $attributeMaker;
    }
}