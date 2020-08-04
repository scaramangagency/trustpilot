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
class BusinessUnitsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Show all the private reviews written about a business unit
     * 
     * @param string $businessUnitId
     * @param int $page
     * @param string $orderBy [createdat.asc, createdat.desc, stars.asc or stars.desc]
     *
     * @return bool|JSON
     */
    public function getBusinessUnitPrivateReviews(string $businessUnitId, int $page = 0, string $orderBy = 'createdat.desc') {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);
        $result->get('https://api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/reviews', array(
            'page' => $page,
            'orderBy' => $orderBy,
            'perPage' => 100
        ));

        $result = $result->response;
        
        if (!isset($result->reviews)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Returns the business unit given by the provided name
     * 
     * @param string $name
     *
     * @return bool|JSON
     */
    public function findBusinessUnit(string $name) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/find', array(
            'name' => $name,
            'apikey' => $apiKey
        ));

        $result = $result->response;
        
        if (!isset($result->displayName)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Show all the public reviews written about a business unit
     * 
     * @param string $businessUnitId
     * @param int $page
     * @param string $orderBy [createdat.asc, createdat.desc, stars.asc or stars.desc]
     *
     * @return bool|JSON
     */
    public function getBusinessUnitReviews(string $businessUnitId, int $page = 0, string $orderBy = 'createdat.desc') {
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

        $result = $result->response;
        
        if (!isset($result->reviews)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Show all links to the business unit's public profile page on Trustpilot
     * 
     * @param string $businessUnitId
     * @param string $locale
     *
     * @return bool|JSON
     */
    public function getBusinessUnitWebLinks(string $businessUnitId, string $locale = 'en-GB') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/web-links', array(
            'apikey' => $apiKey,
            'locale' => $locale
        ));

        $result = $result->response;
        
        if (!isset($result->locale)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Show all business units, regardless of review count
     * 
     * @param string $country
     * @param int $page
     *
     * @return bool|JSON
     */
    public function getAllBusinessUnits(string $country = 'en-GB', int $page) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/all', array(
            'country' => $country,
            'page' => $page,
            'perPage' => 1000
        ));

        $result = $result->response;
        
        if (!isset($result->businessUnits)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Show all business units with one more reviews
     * 
     * @param string $country
     * @param int $page
     *
     * @return bool|JSON
     */
    public function getFilteredBusinessUnits(string $country = 'en-GB', int $page) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units', array(
            'country' => $country,
            'page' => $page,
            'perPage' => 1000
        ));

        $result = $result->response;
        
        if (!isset($result->businessUnits)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get logo for specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitLogo(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/images/logo');

        $result = $result->response;
        
        if (!isset($result->logoUrl)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get the company's Guarantee Box information for a specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitGuaranteeBox(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/customerguarantee');

        $result = $result->response;
        
        if (!isset($result->body)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get the company profile image for a specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitImages(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/images');

        $result = $result->response;
        
        if (!isset($result->profileImage)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get company profile information for a specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitProfileInformation(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/profileinfo');

        $result = $result->response;
        
        if (!isset($result->description)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get the company's Promotion Box information for a specific business unit.
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitProfilePromotion(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/profilepromotion');

        $result = $result->response;
        
        if (!isset($result->description)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get the business unit's basic public information (name, URL, reviews, etc.)
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getBusinessUnitBasicInformation(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId);

        $result = $result->response;
        
        if (!isset($result->displayName)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get a list of categories for a business unit
     * 
     * @param string $businessUnitId
     * @param string $locale
     * @param string $country
     *
     * @return bool|JSON
     */
    public function getBusinessUnitCategoryList(string $businessUnitId, string $locale = 'en-GB', string $country = 'GB') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/categories', array(
            'apikey' => $apiKey,
            'locale' => $locale,
            'country' => $country
        ));

        $result = $result->response;
        
        if (!isset($result->categories)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * This method searches for business units, even if you only know part of the name. 
     * For example, search results for "Trustpilot" would show "trustpilot.com", among others.
     * 
     * @param string $query
     * @param string $country
     * @param int $page
     *
     * @return bool|JSON
     */
    public function searchBusinessUnits(string $query, int $page = 0, string $country = 'GB') {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/search', array(
            'apikey' => $apiKey,
            'page' => $page,
            'query' => $query,
            'perPage' => 100,
            'country' => $country
        ));

        $result = $result->response;
        
        if (!isset($result->businessUnits)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
