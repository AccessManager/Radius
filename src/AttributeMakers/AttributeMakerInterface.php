<?php

namespace AccessManager\Radius\AttributeMakers;

/**
 * Interface AttributeMakerInterface
 * @package AccessManager\Radius\AttributeMakers
 */
interface AttributeMakerInterface
{
    /**
     * @param $username
     * @return mixed
     */
    public function setUsername( $username );

    /**
     * @param $password
     * @return mixed
     */
    public function makePassword( $password );

    /**
     * @param $count
     * @return mixed
     */
    public function makeSimultaneousSessions( $count );

    /**
     * @param $timeLimit
     * @return mixed
     */
    public function makeTimeLimit( $timeLimit );

    /**
     * @param $dataLimit
     * @return mixed
     */
    public function makeDataLimit( $dataLimit );

    /**
     * @param $policy
     * @return mixed
     */
    public function makeBandwidthPolicy( $policy );

    /**
     * @param $seconds
     * @return mixed
     */
    public function makeInterimInterval( $seconds );

    /**
     * @param $seconds
     * @return mixed
     */
    public function makeIdleTimeout( $seconds );

    /**
     * @param $expiry
     * @return mixed
     */
    public function makeExpiration( $expiry );

    /**
     * @param $ip
     * @return mixed
     */
    public function makeFramedIp( $ip );

    /**
     * @param $subnet
     * @param string $framedIp
     * @return mixed
     */
    public function makeFramedRoute( $subnet, $framedIp = '0.0.0.0' );

    /**
     * @return array
     */
    public function getChecks();

    /**
     * @return array
     */
    public function getReplies();
}