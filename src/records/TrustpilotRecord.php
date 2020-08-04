<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\records;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\StringHelper;

/**
 * @author    Scaramanga Agency
 * @package   Craftagram
 * @since     1.2.0
 */
class TrustpilotRecord extends ActiveRecord {
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName(): string {
        return '{{%trustpilot_settings}}';
    }
}