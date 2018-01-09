<?php

namespace AccessManager\Radius\Accounting;


use AccessManager\Radius\AccountSubscriptionWrapper;
use AccessManager\Radius\AttributeMakers\MikrotikAttributeMaker;
use AccessManager\Radius\ReplyMaker;
use Symfony\Component\Process\Process;

class CoA
{
    /**
     * @var ReplyMaker
     */
    protected $replyMaker;
    /**
     * @var
     */
    protected $session;
    /**
     * @var AccountingRequest
     */
    protected $accountingRequest;

    public function invoke()
    {
        $this->replyMaker->addBandwidthPolicy()
                        ->addDataLimit( $this->accountingRequest->sessionData() )
                        ->addTimeLimit( $this->accountingRequest->sessionTime() );

        $attributes = $this->replyMaker->get();
        $shell = null;
        foreach( $attributes as $attribute )
        {
            $shell .= ", {$attribute['attribute']} = ";

            if($attribute['attribute'] == 'Mikrotik-Rate-Limit')
                $shell .= "'";

            $shell .= "{$attribute['value']}";

            if($attribute['attribute'] == 'Mikrotik-Rate-Limit')
                $shell .= "'";
        }

        $exec = "echo \" User-Name={$this->session->username}, Framed-IP-Address= {$this->session->framedipaddress}, Acct-Session-Id= {$this->session->acctsessionid}" .
            $shell . " \" | radclient {$this->session->nasipaddress}:3799 coa {$this->session->secret}";

        $process = new Process($exec);
        $process->run();
    }

    public function __construct( AccountSubscriptionWrapper $subscriptionWrapper, $session, AccountingRequest $accountingRequest )
    {
        $this->replyMaker = new ReplyMaker($subscriptionWrapper, new MikrotikAttributeMaker);
        $this->session = $session;
        $this->accountingRequest = $accountingRequest;
    }
}