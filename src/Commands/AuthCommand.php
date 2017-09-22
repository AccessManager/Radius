<?php

namespace AccessManager\Radius\Commands;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\AttributeMakers\MikrotikAttributeMaker;
use AccessManager\Radius\Auth\Authenticator;
use AccessManager\Radius\Auth\Authorizer;
use Illuminate\Console\Command;

/**
 * Class AuthCommand
 * @package AccessManager\Radius
 */
class AuthCommand extends Command
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
    public function handle( AccountSubscription $accountSubscription )
    {
        $username = $this->argument('username');

        $subscription = $accountSubscription->where('username', $username)->firstOrFail();

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