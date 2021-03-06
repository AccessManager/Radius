<?php

namespace AccessManager\Radius\Accounting;

use Symfony\Component\Process\Process;

class SessionDisconnection
{
    protected $session;

    public function invoke()
    {
        $exec = "echo \" User-Name={$this->session->username}, Framed-IP-Address={$this->session->framedipaddress} \" ".
            "| radclient {$this->session->nasipaddress}:3799 disconnect {$this->session->secret}";
        (new Process($exec) )->run();
    }

    public function __construct( $session )
    {
        $this->session = $session;
    }
}