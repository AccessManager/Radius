<?php

namespace AccessManager\Radius\Commands;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
//use AccessManager\Hotspot\Models\HotspotSubscription;
use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\AttributeMakers\MikrotikAttributeMaker;
use AccessManager\Radius\Auth\Authenticator;
use AccessManager\Radius\Auth\Authorizer;
use AccessManager\Radius\Helpers\Radius;

//use Illuminate\Console\Command;

/**
 * Class AuthCommand
 * @package AccessManager\Radius
 */
class AuthCommand extends RadiusBaseCommand
{
    /**
     * @var string
     */
    protected $signature = 'am:authorize {username : username of the subscription attempting to login.}';

    /**
     * @var string
     */
    protected $description = 'Authenticates user for login.';

    /**
     * @param AccountSubscription $accountSubscription
     */
    public function handle()
    {
        $username = $this->argument('username');

        try{
            $subscription = $this->getSubscriptionFromUsername($username);
        }
        catch (\Exception $e)
        {
            Radius::reject("no such user: {$username}");
        }

        $authenticator = new Authenticator( $subscription );

        $authenticator->checkIsValidSubscription()
            ->checkActivationStatus()
            ->checkHaveServicesAssigned()
            ->checkIfSubscriptionExpired()
            ->checkIfQuotaIsAvailable();

        $authorizer = new Authorizer( new AccountSubscriptionWrapper($subscription), new MikrotikAttributeMaker );

        $authorizer->makeChecks()
            ->makeReplies()
            ->updateRadius();
    }



}