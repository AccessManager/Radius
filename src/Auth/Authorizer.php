<?php

namespace AccessManager\Radius\Auth;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use AccessManager\Radius\AttributeMakers\AttributeMakerInterface;
use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\CheckMaker;
use AccessManager\Radius\ReplyMaker;
use Illuminate\Support\Facades\DB;

class Authorizer
{
    /**
     * @var AccountSubscription|AccountSubscriptionWrapper
     */
    protected $subscription;

    /**
     * @var CheckMaker
     */
    protected $checkMaker;

    /**
     * @var ReplyMaker
     */
    protected $replyMaker;

    /**
     * @return $this
     */
    public function makeChecks()
    {
        $this->checkMaker->addPassword()
                        ->addSimultaneousSessions()
                        ->addExpiration();

        return $this;
    }

    /**
     * @return $this
     */
    public function makeReplies()
    {
        $this->replyMaker->addBandwidthPolicy()
                        ->addDataLimit()
                        ->addTimeLimit()
                        ->addIdleTimeout()
                        ->addFramedIp()
                        ->addFramedRoute()
                        ->addInterimInterval();

        return $this;
    }

    public function updateRadius()
    {
        DB::table('radcheck')->where('username', $this->subscription->username)->delete();
        DB::table('radreply')->where('username', $this->subscription->username)->delete();
        DB::table('radcheck')->insert($this->checkMaker->get());
        DB::table('radreply')->insert($this->replyMaker->get());
    }

    /**
     * Authorizer constructor.
     * @param AccountSubscriptionWrapper $subscription
     * @param AttributeMakerInterface $attributeMaker
     */
    public function __construct( AccountSubscriptionWrapper $subscription, AttributeMakerInterface $attributeMaker )
    {
        $this->subscription = $subscription;

        $attributeMaker->setUsername($subscription->username);

        $this->checkMaker = new CheckMaker( $subscription, $attributeMaker );
        $this->replyMaker = new ReplyMaker( $subscription, $attributeMaker);
    }
}