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
class ReviewLink extends Model
{
    // Public Properties
    // =========================================================================
    public $email;
    public $name;

    // Public Methods
    // =========================================================================
    public function rules()
    {
        return [[['email', 'name'], 'required']];
    }
}
