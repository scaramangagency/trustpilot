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
class ResourcesService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This resource shows you the different sizes that are available, as well as the URLs for the images.
     * 
     * @return bool|JSON
     */
    public function getTrustpilotIcons() {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/icons');

        $result = $result->response;

        return $result;
    }

    /**
     * This resource shows you the different sizes that are available, as well as the URLs for the images.
     * 
     * @return bool|JSON
     */
    public function getTrustpilotLogos() {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/logos');

        $result = $result->response;

        return $result;
    }
}
