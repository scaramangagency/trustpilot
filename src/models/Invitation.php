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
class Invitation extends Model
{
    // Public Properties
    // =========================================================================
    public $email;
    public $name;
    public $templateId;
    public $siteId;

    public $redirectUri;

    // Public Methods
    // =========================================================================
    public function rules(): array
    {
        return [[['email', 'name', 'templateId', 'siteId'], 'required'], ['redirectUri', 'string']];
    }
}
