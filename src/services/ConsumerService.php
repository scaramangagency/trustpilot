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
class ConsumerService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get details of a specific category
     * 
     * @param int $consumerId
     * @param int $page
     * @param string $orderBy [createdat.asc, createdat.desc, stars.asc or stars.desc]
     *
     * @return bool|JSON
     */
    public function getConsumerReviews(int $consumerId, int $page = 0, string $orderBy = 'createdat.desc') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/consumers/' . $consumerId . '/reviews', array(
            'apikey' => $apiKey,
            'orderBy' => $orderBy,
            'perPage' => 100,
            'page' => $page
        ));

        $result = $result->response;
        
        if (!isset($result->reviews)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
