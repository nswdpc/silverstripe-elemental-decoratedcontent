<?php

namespace NSWDPC\Elemental\Models\DecoratedContent;

use DNADesign\Elemental\Models\ElementContent;
use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\TagField\Tagfield;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * Provides an decorated ElementContent with extra-fun fields
 * @note could be added to a module
 * @author James
 * @author Mark
 */
class ElementDecoratedContent extends ElementContent
{
    private static $inline_editable = false;

    private static $singular_name = 'Decorated content';

    private static $plural_name = 'Decorated content';

    private static $table_name = 'ElementDecoratedContent';

    private static $icon = 'font-icon-block-content';

    private static $title = 'Decorated content';

    private static $description = 'A content element with extra fields';

    private static $db = [
        'CallToAction' => 'Varchar(32)',
        'PublicDate' => 'Datetime',
        'UseLastEditedDate' => 'Boolean'
    ];

    private static $defaults = [
        'UseLastEditedDate' => 0
    ];

    private static $has_one = [
        'Image' => Image::class,
        'LinkTarget' => Link::class
    ];

    private static $many_many = [
        'Tags' => TaxonomyTerm::class
    ];

    private static $owns = [
        'Image',
        'LinkTarget'
    ];

    /**
     * Defines a default list of filters for the search context
     * @var array
     */
    private static $searchable_fields = [
        'HTML'
    ];

    /**
     * Get available taxonomy terms
     * @return DataList|null
     */
    protected function getTaxonomyTerms() {
        $list = TaxonomyTerm::get()->sort('Name ASC');
        return $list;
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Decorated Content');
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->UseLastEditedDate == 1) {
            $this->PublicDate = DBDatetime::now();
        }
    }

    public function getCmsFields()
    {
        $fields = parent::getCmsFields();
        $fields->removeByName(['PublicDate','UseLastEditedDate','Tags','LinkTargetID']);
        $fields->addFieldsToTab(
            'Root.Main',
            [
                UploadField::create(
                    'Image',
                    _t(__CLASS__ . '.IMAGE', 'Image')
                )->setTitle(
                    _t(
                        __CLASS__ . '.ADD_IMAGE_TO_CONTENT_BLOCK',
                        'Add an image related to this content'
                    )
                )->setFolderName('blocks/content/' . $this->owner->ID)
                ->setAllowedMaxFileNumber(1)
                ->setIsMultiUpload(false),
                CompositeField::create(
                    DatetimeField::create(
                        'PublicDate',
                        _t(__CLASS__ . '.VISIBLE_DATE', 'Date visible in element')
                    ),
                    CheckboxField::create(
                        'UseLastEditedDate',
                        _t(__CLASS__ . '.USE_LASTEDITED_DATE', 'Just use the last edited date of this record')
                    )
                ),

                Tagfield::create(
                    'Tags',
                    _t(__CLASS__ . '.TAGS', 'Tags'),
                    [],
                    $this->Tags(),
                    'Name' // TaxonomyTerm.Name
                )->setShouldLazyLoad(true)
	             ->setCanCreate(true)
                 ->setSourceList( $this->getTaxonomyTerms() ),

                $this->getLinkField(),

                TextField::create(
                    'CallToAction',
                    _t(__CLASS__ . '.CALL_TO_ACTION', 'Call to action text')
                )
            ]
        );

        return $fields;
    }

    protected function getLinkField() {
        $field = LinkField::create(
            'LinkTarget',
            _t(
                __CLASS__ . '.LINK',
                'Link'
            ),
            $this
        );
        return $field;
    }
}
