<?php

namespace AccessManager\Radius\Accounting;


use AccessManager\Radius\AccountSubscriptionWrapper;

class CoAHandler
{
    /**
     * @var InterimUpdate
     */
    protected $interimUpdate;
    /**
     * @var AccountSubscriptionWrapper
     */
    protected $subscription;

    /**
     * @var array
     */
    protected $activeSessions;

    public function handle()
    {
        $this->_fetchActiveSessions();

        foreach( $this->activeSessions as $session )
        {
            if( $session->type == 'PPP' )
            {
                (new SessionDisconnection($session) )->invoke();
            } else {
                (new CoA( $session ) )->invoke();
            }
        }
    }

    private function _fetchActiveSessions()
    {
        $this->activeSessions = \DB::table( 'radacct as a' )
                                    ->join( 'routers AS r', 'r.nasname', '=', 'a.nasipaddress' )
                                    ->where( 'a.username', $this->interimUpdate->userName )
                                    ->where('a.acctstoptime', NULL)
                                    ->select( 'a.nasipaddress', 'r.secret', 'a.servicetype',
                                        'a.framedipaddress', 'a.acctsessionid' )
                                    ->get();
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