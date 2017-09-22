<?php

namespace AccessManager\Radius\Commands;


use AccessManager\AccountDetails\AccountSubscription\Models\AccountSubscription;
use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\Helpers\Radius;
use AM3\Radius\Accounting\Accountant;
use AM3\Radius\Accounting\CoA;
use AM3\Radius\Accounting\InterimUpdate;
use Illuminate\Console\Command;

class AccountingCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'am:account {z : Freeradius accounting packet.}';

    /**
     * @var string
     */
    protected $description = 'Handles accounting/interim update packets from NASes.';

    public function handle( AccountSubscription $accountSubscription )
    {
        $accountingPacket = $this->argument('z');

        $accountingAttributes = Radius::parseAccountingPacket($accountingPacket);

        $interimUpdate = InterimUpdate::createFromAttributes($accountingAttributes);

        if( $interimUpdate->isNewSession() ) exit(0);

        $subscription = $accountSubscription->where( 'username', $interimUpdate->userName )->firstOrFail();

        $accountant = new Accountant( new AccountSubscriptionWrapper($subscription), $interimUpdate);

        if( $accountant->isNotCountable() )  exit(0);

        $accountant->count();

        if( $accountant->CoARequired() )
        {
            ( new CoA( $interimUpdate, new AccountSubscriptionWrapper($subscription) ) )->invoke();
        }
    }

}