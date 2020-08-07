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
class BusinessUnitsService extends Component
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

    /**
     * Get the business unit's basic public information (name, URL, reviews, etc.)
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getProfile(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId, array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);

        if (!property_exists($result, 'displayName')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        $data = [
            'trustpilotLink' => Trustpilot::$plugin->businessUnitsService->getProfileUrl($businessUnitId),
            'companyLogo' => Trustpilot::$plugin->businessUnitsService->getCompanyLogo($businessUnitId),
            'guaranteeBox' => Trustpilot::$plugin->businessUnitsService->getGuaranteeBox($businessUnitId),
            'profilePicture' => Trustpilot::$plugin->businessUnitsService->getProfileImage($businessUnitId),
            'promotion' => Trustpilot::$plugin->businessUnitsService->getProfilePromotion($businessUnitId),
            'websiteUrl' => $result->websiteUrl,
            'displayName' => $result->displayName,
            'trustScore' => $result->score->trustScore,
            'stars' => $result->score->stars,
            'count' => [
                'total' => $result->numberOfReviews->total,
                'oneStar' => $result->numberOfReviews->oneStar,
                'twoStars' => $result->numberOfReviews->twoStars,
                'threeStars' => $result->numberOfReviews->threeStars,
                'fourStars' => $result->numberOfReviews->fourStars,
                'fiveStars' => $result->numberOfReviews->fiveStars
            ]
        ];

        return $data;
    }

    /**
     * Returns the business unit if it exists in the database, or grabs it from the API if not
     * 
     * @param string $name
     *
     * @return bool|JSON
     */
    public function returnBusinessUnit(string $name) {
        $reinit = false;

        $configRecord = TrustpilotRecord::findOne(1);
        if (!$configRecord) {
            $reinit = true;
        }

        if ($configRecord) {
            $currentTrustpilotUrl = $configRecord->getAttribute('currentTrustpilotUrl') ?? null;
            $businessUnitId = $configRecord->getAttribute('businessUnitId') ?? null;
            
            if ($name != $currentTrustpilotUrl || is_null($businessUnitId)) {
                $reinit = true;
            }
        }
        
        if ($reinit) {
            return Trustpilot::$plugin->businessUnitsService->locateBusinessUnit($name);
        }

        return $businessUnitId;
    }

    public function locateBusinessUnit(string $name) {
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

        $result = json_decode($result->response);
        
        if (!property_exists($result, 'displayName')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin !== null) {
            $configRecord = TrustpilotRecord::findOne(1);
            
            if (!$configRecord) {
                $configRecord = new TrustpilotRecord();
            }

            $configRecord->setAttribute('currentTrustpilotUrl', $name);
            $configRecord->setAttribute('businessUnitId', $result->id);
            $configRecord->setAttribute('createdTimestamp', date('Y-m-d H:m:s', time()));
            $configRecord->setAttribute('dateCreated', date('Y-m-d H:m:s', time()));
            $configRecord->setAttribute('dateUpdated', date('Y-m-d H:m:s', time()));
            $configRecord->save();

            return $result->id;
        }

        return false;
    }

    /**
     * Show all links to the business unit's public profile page on Trustpilot
     * 
     * @param string $businessUnitId
     * @param string $locale
     *
     * @return bool|JSON
     */
    public function getProfileUrl(string $businessUnitId, string $locale = 'en-GB') {
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

        $result = json_decode($result->response);
        
        if (!property_exists($result, 'profileUrl')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result->profileUrl;
    }

    /**
     * Get logo for specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getCompanyLogo(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/images/logo', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);
        
        if (!property_exists($result, 'logoUrl')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result->logoUrl;
    }

    /**
     * Get the company's Guarantee Box information for a specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getGuaranteeBox(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/customerguarantee', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);

        if (!property_exists($result, 'body')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        $data = [
            'body' => $result->body,
            'header' => $result->header,
            'teaser' => $result->teaser,
            'image' => $result->image->image184x0->url ?? null
        ];

        return $data;
    }

    /**
     * Get the company profile image for a specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getProfileImage(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/images', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);
        
        if (!property_exists($result, 'profileImage')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result->profileImage->image198x149->url;
    }

    /**
     * Get the company's Promotion Box information for a specific business unit.
     * 
     * @param string $businessUnitId
     *
     * @return bool|JSON
     */
    public function getProfilePromotion(string $businessUnitId) {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey();

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/profilepromotion', array(
            'apikey' => $apiKey
        ));

        $result = json_decode($result->response);
        
        if (!property_exists($result, 'description')) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        $data = [
            'alternateTitle' => $result->contactInfoTitle,
            'title' => $result->contactInfoTitle,
            'description' => [
                'header' => $result->description->header,
                'text' => $result->description->text
            ],
            'image' => $result->image->image255x0->url,
            'sellingPoints' => []
        ];

        foreach ($result->sellingPoints as $sellingPoint) {
            $arr = [
                'header' => $sellingPoint->header,
                'text' => $sellingPoint->text
            ];

            array_push($data['sellingPoints'], $arr);
        }

        return $data;
    }
}