<?php

namespace AccessManager\Radius\Commands;


//use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use AccessManager\Radius\Accounting\AccountingRequest;
use AccessManager\Radius\Accounting\CoAHandler;
use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\Helpers\Radius;
use AccessManager\Radius\Accounting\Accountant;
//use AccessManager\Radius\Accounting\CoA;
use AccessManager\Radius\Accounting\InterimUpdate;
//use Illuminate\Console\Command;

class AccountingCommand extends RadiusBaseCommand
{
    /**
     * @var string
     */
    protected $signature = 'am:account {User-Name} {Acct-Session-Id} {Acct-Unique-Session-Id} {Acct-Input-Octets} {Acct-Output-Octets} {Acct-Input-Gigawords} {Acct-Output-Gigawords} {Acct-Session-Time} {Acct-Status-Type}';

    /**
     * @var string
     */
    protected $description = 'Handles accounting/interim update packets from NASes.';

    public function handle()
    {
        $accountingPacket = $this->arguments();

//        $accountingAttributes = Radius::parseAccountingPacket($accountingPacket);

        Radius::checkAndIgnoreSessionStartUpdates($accountingPacket);

        $interimUpdate = InterimUpdate::createFromAttributes($accountingPacket);

//        $subscription = $accountSubscription->where( 'username', $interimUpdate->userName )->firstOrFail();

        $subscription = $this->getSubscriptionFromUsername($interimUpdate->userName);

        $accountant = new Accountant(
            new AccountSubscriptionWrapper($subscription),
            new AccountingRequest($interimUpdate)
        );

        if( $accountant->isNotCountable() )  exit(0);

        $accountant->count();

        if( $accountant->CoARequired() )
        {
//            $subscription = $accountSubscription->where( 'username', $interimUpdate->userName )->firstOrFail();
            $subscription = $this->getSubscriptionFromUsername($interimUpdate->userName);

            ( new CoAHandler( $interimUpdate, new AccountSubscriptionWrapper($subscription) ) )->handle();
        }
    }

}