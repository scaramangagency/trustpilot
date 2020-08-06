<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\models;

use scaramangagency\trustpilot\Trustpilot;

use Craft;
use craft\base\Model;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apiKey;
    public $apiSecret;
    public $trustpilotUsername;
    public $trustpilotPassword;


    public $accessToken;
    public $refreshToken;
    public $expiresIn;
    public $createdTimestamp;

    public $trustpilotUrl;
    public $invitationSenderEmail;
    public $invitationSenderName;
    public $invitationReplyToEmail;

    public $businessUnitId;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['apiKey', 'apiSecret', 'trustpilotUsername', 'trustpilotPassword',
              'invitationSenderEmail', 'invitationSenderName', 'invitationReplyToEmail', 'trustpilotUrl'], 'required'],
            [['accessToken', 'refreshToken', 'expiresIn', 'createdTimestamp'], 'string']
        ];
    }
}
