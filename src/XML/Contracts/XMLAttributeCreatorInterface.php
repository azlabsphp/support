<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\XML\Contracts;

interface XMLAttributeCreatorInterface
{
    /**
     * Create an xml string represeting the configuration
     * passed as parameter.
     *
     * @param XMLAttributeInterface[]|XMLAttributeInterface|array $config
     *
     * @return \XMLWriter|mixed
     */
    public function create($config);
}
