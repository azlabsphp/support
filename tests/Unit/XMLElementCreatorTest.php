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

namespace Drewlabs\Support\Tests\Unit;

use function Drewlabs\Support\Proxy\XMLAttribute;
use function Drewlabs\Support\Proxy\XMLElement;

use Drewlabs\Support\Tests\TestCase;
use Drewlabs\Support\XML\XMLElementCreator;

class XMLElementCreatorTest extends TestCase
{
    public function testCreateXMLElement()
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $element = (new XMLElementCreator())->create(
            XMLElement(
                'BaseRequest',
                XMLElement(
                    'RequestData',
                    XMLElement(
                        'PingID',
                        99
                    ),
                    'ser',
                    XMLAttribute(
                        'xsi:type',
                        'PingRequest'
                    ),
                    'http://gtplimited.com/',
                ),
                'gtp'
            )
        );

        file_put_contents(__DIR__.'/../Stubs/resources.xml', $element);
        $xml->startDocument();
        $xml->writeRaw($element);
        $xml->endDocument();
        $this->assertSame($element, '<gtp:BaseRequest><ser:RequestData xsi:type="PingRequest" xmlns="http://gtplimited.com/"><PingID>99</PingID></ser:RequestData></gtp:BaseRequest>');
    }
}
