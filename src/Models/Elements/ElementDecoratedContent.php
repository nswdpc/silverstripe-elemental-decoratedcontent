<?php

namespace NSWDPC\Elemental\Models\DecoratedContent;

use DNADesign\Elemental\Models\ElementContent;
use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * Provides an decorated ElementContent with extra-fun fields
 * @note could be added to a module
 * @author James
 * @author Mark
 * @property ?string $Subtitle
 * @property ?string $CallToAction
 * @property ?string $PublicDate
 * @property bool $UseLastEditedDate
 * @property ?string $ImageAlignment
 * @property ?string $IconClass
 * @property ?string $Video
 * @property ?string $Provider
 * @property int $ImageID
 * @property int $LinkTargetID
 * @method \SilverStripe\Assets\Image Image()
 * @method \gorriecoe\Link\Models\Link LinkTarget()
 * @method \SilverStripe\ORM\ManyManyList<\SilverStripe\Taxonomy\TaxonomyTerm> Tags()
 */
class ElementDecoratedContent extends ElementContent
{
    /**
     * @inheritdoc
     */
    private static bool $inline_editable = false;

    /**
     * @inheritdoc
     */
    private static string $singular_name = 'Decorated content';

    /**
     * @inheritdoc
     */
    private static string $plural_name = 'Decorated content';

    /**
     * @inheritdoc
     */
    private static string $table_name = 'ElementDecoratedContent';

    /**
     * @inheritdoc
     */
    private static string $icon = 'font-icon-block-banner';

    /**
     * @inheritdoc
     */
    private static string $title = 'Decorated content';

    /**
     * @inheritdoc
     */
    private static string $class_description = 'A content element with extra fields';

    /**
     * @inheritdoc
     */
    private static array $db = [
        'Subtitle' => 'Varchar(255)',
        'CallToAction' => 'Varchar(32)',
        'PublicDate' => 'Datetime',
        'UseLastEditedDate' => 'Boolean',
        'ImageAlignment' => 'Varchar(32)',
        'IconClass' => 'Varchar(64)',
        'Video' => 'Varchar(255)',
        'Provider' => 'Varchar'
    ];

    /**
     * @inheritdoc
     */
    private static array $defaults = [
        'UseLastEditedDate' => 0
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'Image' => Image::class,
        'LinkTarget' => Link::class
    ];

    /**
     * @inheritdoc
     */
    private static array $many_many = [
        'Tags' => TaxonomyTerm::class
    ];

    /**
     * @inheritdoc
     */
    private static array $owns = [
        'Image',
        'LinkTarget'
    ];

    /**
     * Defines a default list of filters for the search context
     */
    private static array $searchable_fields = [
        'HTML'
    ];

    /**
     * Defines available video providers
     */
    private static array $video_providers = [
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo'
    ];

    /**
     * Get available taxonomy terms
     */
    protected function getTaxonomyTerms(): \SilverStripe\ORM\DataList
    {
        return TaxonomyTerm::get()->sort('Name ASC');
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Decorated Content');
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->UseLastEditedDate == 1) {
            $this->PublicDate = DBDatetime::now()->Format(DBDateTime::ISO_DATETIME);
        }
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getCmsFields()
    {
        $fields = parent::getCmsFields();
        $fields->removeByName(['PublicDate','UseLastEditedDate','Tags','LinkTargetID', 'Provider']);
        $fields->insertAfter('Main', Tab::create('Image', _t(self::class . '.IMAGE', 'Image')));
        $fields->addFieldsToTab(
            'Root.Image',
            [
                UploadField::create(
                    'Image',
                    _t(self::class . '.IMAGE', 'Image')
                )->setTitle(
                    _t(
                        self::class . '.ADD_IMAGE_TO_CONTENT_BLOCK',
                        'Add an image related to this content'
                    )
                )->setFolderName('blocks/content/' . $this->owner->ID)
                ->setAllowedMaxFileNumber(1)
                ->setIsMultiUpload(false),
                DropdownField::create(
                    'ImageAlignment',
                    _t(self::class . '.IMAGE_ALIGNMENT', 'Image alignment'),
                    [
                        'left' => _t(self::class . '.LEFT', 'Left'),
                        'right' => _t(self::class . '.RIGHT', 'Right')
                    ]
                )->setEmptyString('Choose an option')
                ->setDescription(
                    _t(self::class . '.IMAGE_ALIGNMENT_DESCRIPTION', 'Use of this option is dependent on the theme')
                )
            ]
        );

        // Video fields
        $fields->insertAfter('Image', Tab::create('Video', _t(self::class . '.VIDEO', 'Video')));
        $fields->addFieldsToTab(
            'Root.Video',
            [
                OptionsetField::create(
                    'Provider',
                    _t(self::class . '.PROVIDER', 'Video provider'),
                    $this->config()->get('video_providers')
                ),
                TextField::create(
                    'Video',
                    _t(
                        self::class . 'VIDEO_PROVIDER_ID',
                        "Provider's video identification code"
                    )
                )->setDescription(
                    _t(
                        self::class . 'VIDEO_PROVIDER_ID_DESCRIPTION',
                        "This is the id number or code for the video, eg '123456' or 'abcd1234' displayed by the provider, usually found in their share widget"
                    )
                )
            ]
        );

        $fields->insertAfter('Video', Tab::create('Meta', _t(self::class . '.META', 'Meta')));
        $fields->addFieldsToTab(
            'Root.Meta',
            [
                CompositeField::create(
                    DatetimeField::create(
                        'PublicDate',
                        _t(self::class . '.VISIBLE_DATE', 'Date visible in element')
                    ),
                    CheckboxField::create(
                        'UseLastEditedDate',
                        _t(self::class . '.USE_LASTEDITED_DATE', 'Just use the last edited date of this record')
                    )
                )->setTitle(_t(self::class . '.DATE_OPTIONS', 'Date options')),

                Tagfield::create(
                    'Tags',
                    _t(self::class . '.TAGS', 'Tags'),
                    [],
                    $this->Tags(),
                    'Name' // TaxonomyTerm.Name
                )->setShouldLazyLoad(true)
                 ->setCanCreate(true)
                 ->setSourceList($this->getTaxonomyTerms()),

                TextField::create(
                    'IconClass',
                    _t(self::class . '.ICON_CLASS', 'An icon class, reference or ligature')
                )->setDescription(
                    _t(self::class . '.ICON_CLASS_DESCRIPTION', 'Use of this option is dependent on the theme in use')
                )
            ]
        );

        $fields->insertAfter(
            'Title',
            TextField::create(
                'Subtitle',
                _t(self::class . '.SUBTITLE', 'Subtitle')
            )->setDescription(
                _t(self::class . '.SUBTITLE_DESCRIPTION', 'An optional sub-title, such as a byline. The display of this field is dependent on the theme in use')
            )
        );

        $fields->insertAfter(
            'Subtitle',
            TextField::create(
                'CallToAction',
                _t(self::class . '.CALL_TO_ACTION', 'Call to action text')
            )->setDescription(
                _t(self::class . '.CALL_TO_ACTION_DESCRIPTION', 'An optional call-to-action text to use within the element. The display of this field is dependent on the theme in use')
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
    protected function getLinkField(): LinkField
    {
        return LinkField::create(
            'LinkTarget',
            _t(
                self::class . '.LINK',
                'Link'
            ),
            $this
        )->setDescription(
            _t(self::class . '.LINK_DESCRIPTION', 'Choose where this content item will link to')
        );
    }

    /**
     * Compatability method to align with other content elements
     */
    public function ContentLink(): ?Link
    {
        return $this->LinkTarget();
    }

    /**
     * Compatability method to align with other content elements
     */
    public function ContentImage(): ?Image
    {
        return $this->Image();
    }
}
