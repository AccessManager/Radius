<?php

namespace AccessManager\Radius\Accounting;


class CoA
{
    protected $session;

    public function invoke()
    {

    }

    public function __construct( $session )
    {
        $this->session = $session;
    }
}