<?php

namespace AccessManager\Radius\Accounting;


use AccessManager\Constants\Data;

class AccountingRequest
{
    protected $interimUpdate;
    protected $lastUpdate;

    public function countableInputData()
    {
        return $this->sessionInputData() - $this->lastUpdate->acctinputoctets;
    }

    public function countableOutputData()
    {
        return $this->sessionOutputData() - $this->lastUpdate->acctoutputoctets;
    }

    public function countableData()
    {
        return $this->countableInputData() + $this->countableOutputData();
    }

    public function countableTime()
    {
        return $this->sessionTime() - $this->lastUpdate->acctsessiontime;
    }

    public function sessionInputData()
    {
        return $this->interimUpdate->acctInputOctets + (
            $this->interimUpdate->acctInputGigawords * Data::FOUR_GB
            );
    }

    public function sessionOutputData()
    {
        return $this->interimUpdate->acctOutputOctets + (
                $this->interimUpdate->acctOutputGigawords * Data::FOUR_GB
            );
    }

    public function sessionData()
    {
        return $this->sessionInputData() + $this->sessionOutputData();
    }

    public function sessionTime()
    {
        return $this->interimUpdate->acctSessionTime;
    }

    private function _fetchLastUpdate()
    {
        $this->lastUpdate = \DB::table('radacct')
            ->where([
                'username'              => $this->interimUpdate->userName,
                'acctuniqueid'          =>  $this->interimUpdate->acctUniqueId,
                'acctuniquesessionid'   =>  $this->interimUpdate->acctUniqueSessionId,
            ])
            ->first();
    }

    public function __construct( InterimUpdate $interimUpdate )
    {
        $this->interimUpdate = $interimUpdate;
        $this->_fetchLastUpdate();
    }
}