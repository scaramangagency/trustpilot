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
class CategoriesService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get details of a specific category
     * 
     * @param int $categoryId
     * @param string $locale
     * @param string $country
     *
     * @return bool|JSON
     */
    public function getCategoriesByCategoryId(int $categoryId, string $locale = 'en-GB', string $country = 'GB') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/categories/' . $categoryId, array(
            'apikey' => $apiKey,
            'locale' => $locale,
            'country' => $country
        ));

        $result = $result->response;
        
        if (!isset($result->businessUnitCount)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get a list of business units in a specific category
     * 
     * @param int $categoryId
     * @param string $locale
     * @param string $country
     * @param int $page
     *
     * @return bool|JSON
     */
    public function getBusinessUnitsByCategory(int $categoryId, string $locale = 'en-GB', string $country = 'GB', int $page = 1) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/categories/' . $categoryId . '/business-units', array(
            'apikey' => $apiKey,
            'locale' => $locale,
            'country' => $country,
            'page' => $page,
            'perPage' => 100
        ));

        $result = $result->response;
        
        if (!isset($result->businessUnits)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get a list of all categories
     * 
     * @param string $locale
     * @param string $country
     * @param string $parentId
     *
     * @return bool|JSON
     */
    public function getAllCategories(string $locale = 'en-GB', string $country = 'GB', string $parentId = '') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/categories', array(
            'apikey' => $apiKey,
            'locale' => $locale,
            'country' => $country,
            'parentId' => $parentId
        ));

        $result = $result->response;
        
        if (!isset($result->categories)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
