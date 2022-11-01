<?php

namespace NSWDPC\Elemental\Models\DecoratedContent\Tests;

use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use NSWDPC\Elemental\Models\DecoratedContent\ElementDecoratedContent;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBDatetime;

/**
 * Unit test to verify field handling in element
 * @author James
 */
class ElementDecoratedContentTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testPublicDateUseLastEdited()
    {
        $now = '2022-12-31 14:25:34';
        DBDatetime::set_mock_now($now);

        $field = ElementDecoratedContent::create([
            'Title' => 'Test public date',
            'UseLastEditedDate' => 1
        ]);
        $field->write();

        $this->assertEquals($now, $field->PublicDate, "Public date should be mocked now datetime");

        DBDatetime::clear_mock_now();
    }

    public function testPublicDateNotUseLastEdited()
    {
        $publicDate = '1922-12-31 14:25:34';

        $field = ElementDecoratedContent::create([
            'Title' => 'Test public date',
            'PublicDate' => $publicDate,
            'UseLastEditedDate' => 0
        ]);
        $field->write();

        $this->assertEquals($publicDate, $field->PublicDate, "Public date should be value set");
    }
}
