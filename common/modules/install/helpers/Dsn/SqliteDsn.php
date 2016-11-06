<?php

namespace install\helpers\Dsn;
use install\helpers\Dsn;

/**
 * SqliteDsn
 *
 */
class SqliteDsn extends Dsn
{

    protected function parseDsn()
    {
        $this->parseDsn['database'] = $this->parse_url['path'];
        return true;
    }

}