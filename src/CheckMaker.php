<?php

namespace AccessManager\Radius;

use AccessManager\Radius\AttributeMakers\AttributeMakerInterface;


class CheckMaker
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
    public function addPassword()
    {
        $this->attributesMaker->makePassword( $this->subscription->password );

        return $this;
    }

    /**
     * @return $this
     */
    public function addExpiration()
    {
        if( $this->subscription->expires_on != null )
        {
            $this->attributesMaker->makeExpiration( $this->subscription->expires_on->format('d M Y H:i') );
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addSimultaneousSessions()
    {
        $this->attributesMaker->makeSimultaneousSessions( $this->subscription->sim_sessions );

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->attributesMaker->getChecks();
    }

    /**
     * CheckMaker constructor.
     * @param AccountSubscriptionWrapper $subscription
     * @param AttributeMakerInterface $attributeMaker
     */
    public function __construct( AccountSubscriptionWrapper $subscription, AttributeMakerInterface $attributeMaker )
    {
        $this->subscription = $subscription;
        $this->attributesMaker = $attributeMaker;
    }
}