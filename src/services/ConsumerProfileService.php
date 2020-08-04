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
class ConsumerProfileService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a list of consumer profiles
     * 
     * @param array $consumerIds An array of consumer IDs
     *
     * @return bool|JSON
     */
    public function getConsumerProfiles(array $consumerIds) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/consumers/profile/bulk', array(
            'consumerIds' => $consumerIds
        ));

        $result = $result->response;
        
        if (!isset($result->consumers)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Gets the profile information of the consumer along with the number of reviews
     * 
     * @param string $consumerId
     *
     * @return bool|JSON
     */
    public function getConsumerById(string $consumerId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/consumers/' . $consumerId);

        $result = $result->response;
        
        if (!isset($result->displayName)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Gets the profile information of the consumer along with the number of reviews
     * 
     * @param string $consumerId
     * @param string $locale
     *
     * @return bool|JSON
     */
    public function getConsumerWebLinks(string $consumerId, string $locale = 'en-GB') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/consumers/' . $consumerId . '/web-links', array(
            'locale' => $locale
        ));

        $result = $result->response;
        
        if (!isset($result->profileUrl)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
