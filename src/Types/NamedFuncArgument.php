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

namespace Drewlabs\Support\Types;

use Drewlabs\Contracts\Support\NamedFuncArgument as NamedFuncArgumentInterface;
use Drewlabs\Support\Types\Traits\FuncArgument;

class NamedFuncArgument implements NamedFuncArgumentInterface
{
    use FuncArgument;

    /**
     * Parameter holding the state of the parameter.
     *
     * @var string|int
     */
    private $state;

    /**
     * Property holding the parameter type.
     *
     * @var string|mixed
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        string $name = 'unknown',
        ?string $type = AbstractTypes::ANY,
        ?string $state = FuncArgumentEnum::REQUIRED
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->state = $state;
    }

    /**
     * Handle type conversion to string.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->getType(), $this->isOptional() ? FuncArgumentEnum::OPTIONAL : FuncArgumentEnum::REQUIRED);
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
