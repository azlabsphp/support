<?php

namespace Drewlabs\Support\DI;

use Attribute;

if (version_compare(phpversion(), '8.0', '>')) {

    #[Attribute(Attribute::TARGET_CLASS)]
    class AutoResolve
    {
    }
}
