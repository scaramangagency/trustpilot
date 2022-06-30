<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
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
    public $apiKey;
    public $apiSecret;
    public $trustpilotUsername;
    public $trustpilotPassword;
    public $siteId;

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
    public function rules(): array
    {
        return [
            [
                [
                    'apiKey',
                    'apiSecret',
                    'trustpilotUsername',
                    'trustpilotPassword',
                    'invitationSenderEmail',
                    'invitationSenderName',
                    'invitationReplyToEmail',
                    'trustpilotUrl',
                    'siteId'
                ],
                'required'
            ],
            [['accessToken', 'refreshToken', 'expiresIn', 'createdTimestamp'], 'string']
        ];
    }
}
