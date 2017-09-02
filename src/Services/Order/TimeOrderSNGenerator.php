<?php

namespace Viviniko\Sale\Services\Order;

use Carbon\Carbon;

class TimeOrderSNGenerator extends BaseOrderSNGenerator {

    protected $pool = '0123456789';

    /**
     * generate sequence number
     *
     * @return string
     */
    public function generate()
    {
        $sn = $this->prefix . (new Carbon())->timezone('Asia/Shanghai')->format('ymdHi');
        $sn .= $this->random($this->pool, self::LENGTH - strlen($sn));

        return $this->unique($sn);
    }

}