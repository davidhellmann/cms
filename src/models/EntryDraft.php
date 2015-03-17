<?php
/**
 * @link http://buildwithcraft.com/
 * @copyright Copyright (c) 2013 Pixel & Tonic, Inc.
 * @license http://buildwithcraft.com/license
 */

namespace craft\app\models;

use Craft;
use craft\app\enums\AttributeType;
use craft\app\models\EntryDraft as EntryDraftModel;

Craft::$app->requireEdition(Craft::Client);

/**
 * Class EntryDraft model.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class EntryDraft extends BaseEntryRevisionModel
{
	// Static
	// =========================================================================

	/**
	 * @inheritdoc
	 *
	 * @param static $model
	 * @param array  $config
	 */
	public static function populateModel($model, $config)
	{
		// Merge the draft and entry data
		$entryData = $config['data'];
		$fieldContent = isset($entryData['fields']) ? $entryData['fields'] : null;
		$config['draftId'] = $config['id'];
		$config['id'] = $config['entryId'];
		$config['revisionNotes'] = $config['notes'];
		$title = $entryData['title'];
		unset($config['data'], $entryData['fields'], $config['entryId'], $config['notes'], $entryData['title']);
		$config = array_merge($config, $entryData);

		parent::populateModel($model, $config);

		if ($title)
		{
			$model->getContent()->title = $title;
		}

		if ($fieldContent)
		{
			$model->setContentFromRevision($fieldContent);
		}
	}

	// Properties
	// =========================================================================

	/**
	 * @var integer ID
	 */
	public $id;

	/**
	 * @var boolean Enabled
	 */
	public $enabled = true;

	/**
	 * @var boolean Archived
	 */
	public $archived = false;

	/**
	 * @var string Locale
	 */
	public $locale = 'en-US';

	/**
	 * @var boolean Locale enabled
	 */
	public $localeEnabled = true;

	/**
	 * @var string Slug
	 */
	public $slug;

	/**
	 * @var string URI
	 */
	public $uri;

	/**
	 * @var \DateTime Date created
	 */
	public $dateCreated;

	/**
	 * @var \DateTime Date updated
	 */
	public $dateUpdated;

	/**
	 * @var integer Root
	 */
	public $root;

	/**
	 * @var integer Lft
	 */
	public $lft;

	/**
	 * @var integer Rgt
	 */
	public $rgt;

	/**
	 * @var integer Level
	 */
	public $level;

	/**
	 * @var integer Section ID
	 */
	public $sectionId;

	/**
	 * @var integer Type ID
	 */
	public $typeId;

	/**
	 * @var integer Author ID
	 */
	public $authorId;

	/**
	 * @var \DateTime Post date
	 */
	public $postDate;

	/**
	 * @var \DateTime Expiry date
	 */
	public $expiryDate;

	/**
	 * @var integer New parent ID
	 */
	public $newParentId;

	/**
	 * @var string Revision notes
	 */
	public $revisionNotes;

	/**
	 * @var integer Creator ID
	 */
	public $creatorId;

	/**
	 * @var integer Draft ID
	 */
	public $draftId;

	/**
	 * @var string Name
	 */
	public $name;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'slug' => Craft::t('app', 'Slug'),
			'uri' => Craft::t('app', 'URI'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['locale'], 'craft\\app\\validators\\Locale'],
			[['dateCreated'], 'craft\\app\\validators\\DateTime'],
			[['dateUpdated'], 'craft\\app\\validators\\DateTime'],
			[['root'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['lft'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['rgt'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['level'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['sectionId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['typeId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['authorId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['postDate'], 'craft\\app\\validators\\DateTime'],
			[['expiryDate'], 'craft\\app\\validators\\DateTime'],
			[['newParentId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['creatorId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['draftId'], 'number', 'min' => -2147483648, 'max' => 2147483647, 'integerOnly' => true],
			[['id', 'enabled', 'archived', 'locale', 'localeEnabled', 'slug', 'uri', 'dateCreated', 'dateUpdated', 'root', 'lft', 'rgt', 'level', 'sectionId', 'typeId', 'authorId', 'postDate', 'expiryDate', 'newParentId', 'revisionNotes', 'creatorId', 'draftId', 'name'], 'safe', 'on' => 'search'],
		];
	}
}
