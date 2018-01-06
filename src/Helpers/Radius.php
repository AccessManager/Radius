<?php

namespace AccessManager\Radius\Helpers;

use Symfony\Component\Process\Process;

class Radius
{
    public static function reject( $message )
    {
        echo "Reply-Message := \"$message\"";
        exit(1);
    }

    public static function parseAccountingPacket( $accountingPacket )
    {
        $output = preg_replace("/\s+[=]\s+/",'=', $accountingPacket);
        $output = preg_replace("/\s+/",' ',$output);
        $output = explode(' ',$output);
        $result = [];
        foreach($output as $pair) {
            if(strpos($pair,'=') ){
                list($k,$v) = explode('=',$pair);
                $result[$k] = trim($v,'"');
            }
        }
        return $result;
    }

    public static function checkAndIgnoreSessionStartUpdates( array $accountingAttributes )
    {
        if(
            $accountingAttributes['Acct-Status-Type'] == 'Accounting-On' ||
            $accountingAttributes['Acct-Status-Type'] == 'Start'
        )
            exit(0);
    }

    public static function disconnectSession( $session )
    {
        $exec = "echo \" User-Name={$session->uname}, Framed-IP-Address={$session->framedipaddress} \" ".
            "| radclient {$session->nasipaddress}:3799 disconnect {$session->secret}";
        ( new Process($exec) )->start();
    }
}