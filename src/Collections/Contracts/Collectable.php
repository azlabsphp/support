<?php

namespace Drewlabs\Support\Collections\Contracts;

interface Collectable
{

    /**
     * Collect the output of the a given data structure
     * 
     * @param CollectorInterface|callable $collector 
     * @return mixed 
     * @throws Exception 
     */
    public function collect(callable $collector);
}
