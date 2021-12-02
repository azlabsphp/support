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

namespace Drewlabs\Support\XML;

use Drewlabs\Support\XML\Contracts\XMLAttributeCreatorInterface;
use Drewlabs\Support\XML\Contracts\XMLElementCreatorInterface;
use Drewlabs\Support\XML\Contracts\XMLElementInterface;
use XMLWriter;

class XMLElementCreator implements XMLElementCreatorInterface
{
    /**
     * Private instance of {XMLWriter}.
     *
     * @var \XMLWriter
     */
    private $xml;

    /**
     * Private instance of {XMLAttributeCreatorInterface}.
     *
     * @var XMLAttributeCreatorInterface
     */
    private $attributeCreator;

    public function __construct(
        ?\XMLWriter $xml = null,
        ?XMLAttributeCreatorInterface $attributeCreator = null
    ) {
        if (null === $xml) {
            $xml = new \XMLWriter();
            $xml->openMemory();
        }
        $this->xml = $xml;
        $this->attributeCreator = $attributeCreator ?? new XMLAttributeCreator($xml);
    }

    public function create(XMLElementInterface $node)
    {
        if ($node->namespace() && !empty($node->namespace())) {
            $startElement = sprintf('%s%s', $node->namespace().':', $node->name());
        } else {
            $startElement = sprintf('%s', $node->name());
        }
        $this->xml->startElement($startElement);

        // Write XML Element attributes
        $this->createNodeAttributesIfExists($node->attributes());

        // Write XML element values
        $value = $node->value();
        if ($value && \is_array($value) && \count($value)) {
            foreach ($value as $value_) {
                // code...
                $this->xml->writeRaw($this->create($value_));
            }
        }
        if (!($value instanceof XMLElementInterface) && !\is_array($value)) {
            $this->xml->text((string) ($value ?? ''));
        }
        $this->xml->endElement();

        return $this->xml->outputMemory();
    }

    private function createNodeAttributesIfExists($attributes)
    {
        if ($attributes && \is_array($attributes) && \count($attributes)) {
            foreach ($attributes as $value) {
                $this->xml = $this->attributeCreator->create($value);
            }
        }
    }
}
