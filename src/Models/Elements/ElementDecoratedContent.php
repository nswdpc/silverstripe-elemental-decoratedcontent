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
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\Tab;
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

    private static $icon = 'font-icon-block-banner';

    private static $title = 'Decorated content';

    private static $description = 'A content element with extra fields';

    private static $db = [
        'Subtitle' => 'Varchar(255)',
        'CallToAction' => 'Varchar(32)',
        'PublicDate' => 'Datetime',
        'UseLastEditedDate' => 'Boolean',
        'ImageAlignment' => 'Varchar(32)',
        'IconClass' => 'Varchar(64)',
        'Video' => 'Varchar(255)',
        'Provider' => 'Varchar'
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
        $fields->removeByName(['PublicDate','UseLastEditedDate','Tags','LinkTargetID', 'Provider']);
        $fields->insertAfter('Main', Tab::create('Image', _t(__CLASS__ . '.IMAGE','Image')));
        $fields->addFieldsToTab(
            'Root.Image',
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
                DropdownField::create(
                    'ImageAlignment',
                    _t(__CLASS__ . '.IMAGE_ALIGNMENT', 'Image alignment'),
                    [
                        'left' => _t(__CLASS__ . '.LEFT', 'Left'),
                        'right' => _t(__CLASS__ . '.RIGHT', 'Right')
                    ]
                )->setEmptyString('Choose an option')
                ->setDescription(
                    _t(__CLASS__ . '.IMAGE_ALIGNMENT_DESCRIPTION', 'Use of this option is dependent on the theme')
                )
            ]
        );

        // Video fields
        $fields->insertAfter('Image', Tab::create('Video', _t(__CLASS__ . '.VIDEO','Video')));
        $fields->addFieldsToTab(
            'Root.Video',
            [
                OptionsetField::create(
                    'Provider',
                    _t(__CLASS__ . '.PROVIDER', 'Video provider'),
                    [
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo'
                    ]
                ),
                TextField::create(
                    'Video',
                    _t(
                        __CLASS__ . 'VIDEO_PROVIDER_ID', 'Provider\'s video identification code'
                    )
                )->setDescription(
                    _t(
                        __CLASS__ . 'VIDEO_PROVIDER_ID_DESCRIPTION', 'This is the id number or code for the video, eg \'123456\' or \'abcd1234\' displayed by the provider, usually found in their share widget'
                    )
                )
            ]
        );

        $fields->insertAfter('Video', Tab::create('Meta', _t(__CLASS__ . '.META','Meta')));
        $fields->addFieldsToTab(
            'Root.Meta',
            [
                CompositeField::create(
                    DatetimeField::create(
                        'PublicDate',
                        _t(__CLASS__ . '.VISIBLE_DATE', 'Date visible in element')
                    ),
                    CheckboxField::create(
                        'UseLastEditedDate',
                        _t(__CLASS__ . '.USE_LASTEDITED_DATE', 'Just use the last edited date of this record')
                    )
                )->setTitle( _t(__CLASS__ . '.DATE_OPTIONS', 'Date options') ),

                Tagfield::create(
                    'Tags',
                    _t(__CLASS__ . '.TAGS', 'Tags'),
                    [],
                    $this->Tags(),
                    'Name' // TaxonomyTerm.Name
                )->setShouldLazyLoad(true)
	             ->setCanCreate(true)
                 ->setSourceList( $this->getTaxonomyTerms() ),

                TextField::create(
                    'IconClass',
                    _t(__CLASS__ . '.ICON_CLASS', 'An icon class, reference or ligature')
                )->setDescription(
                    _t(__CLASS__ . '.ICON_CLASS_DESCRIPTION', 'Use of this option is dependent on the theme in use')
                )
            ]
        );

        $fields->insertAfter(
            'Title',
            TextField::create(
                'Subtitle',
                _t(__CLASS__ . '.SUBTITLE', 'Subtitle')
            )->setDescription(
                _t(__CLASS__ . '.SUBTITLE_DESCRIPTION', 'An optional sub-title, such as a byline. The display of this field is dependent on the theme in use')
            )
        );

        $fields->insertAfter(
            'Subtitle',
            TextField::create(
                'CallToAction',
                _t(__CLASS__ . '.CALL_TO_ACTION', 'Call to action text')
            )->setDescription(
                _t(__CLASS__ . '.CALL_TO_ACTION_DESCRIPTION', 'An optional call-to-action text to use within the element. The display of this field is dependent on the theme in use')
            )
        );

        $fields->insertAfter(
            'CallToAction',
            $this->getLinkField()
        );

        return $fields;
    }

    /**
     * Return the field used to handle linking
     */
    protected function getLinkField() : LinkField {
        $field = LinkField::create(
            'LinkTarget',
            _t(
                __CLASS__ . '.LINK',
                'Link'
            ),
            $this
        )->setDescription(
            _t(__CLASS__ . '.LINK_DESCRIPTION','Choose where this content item will link to')
        );
        return $field;
    }
}
