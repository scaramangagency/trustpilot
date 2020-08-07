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
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;

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
class ReviewsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Show all the public reviews written about a business unit
     * 
     * @param string $businessUnitId
     * @param int $page
     * @param string $orderBy [createdat.asc, createdat.desc, stars.asc or stars.desc]
     *
     * @return bool|JSON
     */
    public function getReviews(string $businessUnitId, int $page = 1, string $orderBy = 'createdat.desc') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/reviews', array(
            'apikey' => $apiKey,
            'page' => $page,
            'orderBy' => $orderBy,
            'perPage' => 100
        ));

        $result = json_decode($result->response);

        if (!property_exists($result, 'reviews')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
          
}