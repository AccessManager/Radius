<?php

namespace AccessManager\Radius;


use AccessManager\Radius\AttributeMakers\AttributeMakerInterface;

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
     * @return $this
     */
    public function addTimeLimit()
    {
        if( $this->subscription->haveTimeLimit() && $this->subscription->aqNotAllowed() )
        {
            $this->attributesMaker->makeTimeLimit($this->subscription->timeBalance());
        } else {
            $this->attributesMaker->makeTimeLimit(0);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addDataLimit()
    {
        if( $this->subscription->haveDataLimit() && $this->subscription->aqNotAllowed() )
        {
            $this->attributesMaker->makeDataLimit($this->subscription->dataBalance());
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

    public function addIdleTimeout()
    {
        //TODO: Implement IdleTimeout.
        return $this;
    }

    public function addFramedIp()
    {
        //TODO: implement framed IP.
        return $this;
    }

    public function addFramedRoute()
    {
        //TODO: implement framed route.
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