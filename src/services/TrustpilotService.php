<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\services;

use scaramangagency\trustpilot\Trustpilot;

use Craft;
use craft\base\Component;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class TrustpilotService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Trustpilot::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
