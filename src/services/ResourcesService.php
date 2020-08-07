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
use scaramangagency\trustpilot\services\AuthenticationService;

use Craft;
use craft\base\Component;
use craft\services\Plugins;
use putyourlightson\logtofile\LogToFile;
use Curl\Curl;

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
     * Get the Trustpilot stars.
     * @param string $score [0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5]
     * 
     * @return bool|JSON
     */
    public function getTrustpilotStarImage(string $score) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/stars/' . $score, array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);

        return $result;
    }

    /**
     * Represent the TrustScore as Text. For example: Excellent
     * @param string $score [0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5]
     * 
     * @return bool|JSON
     */
    public function getTrustpilotStarVerbose(string $score) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/strings/stars/' . $score, array(
            'apikey' => $apiKey
        ));
        
        $result = json_decode($result->response);

        return $result;
    }

    /**
     * Get Trustpilot icons
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
        $result->get('https://api.trustpilot.com/v1/resources/images/icons', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);

        return $result;
    }

    /**
     * Get Trustpilot logos
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
        $result->get('https://api.trustpilot.com/v1/resources/images/logos', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);

        return $result;
    }
}
