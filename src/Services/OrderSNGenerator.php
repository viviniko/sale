<?php

namespace Viviniko\Sale\Services;

interface OrderSNGenerator {

    /**
     * sequence number length
     */
    const LENGTH = 17;

    /**
     * The prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function prefix($prefix);

    /**
     * generate sequence number
     *
     * @return string
     */
    public function generate();

}