<?php

namespace install\helpers\Dsn;
use install\helpers\Dsn;

/**
 * OciDsn
 *
 */
class OciDsn extends Dsn
{

    public function init()
    {
        $this->dsn = str_replace("dbname=", "", $this->dsn);
        parent::init();
    }

}