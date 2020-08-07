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
class ProfileService extends Component
{
    // Public Methods
    // =========================================================================
    /**
     * Get the business unit's basic public information (name, URL, reviews, etc.)
     * 
     * @param string $businessUnitId
     *
     * @return bool|array
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
            'companyLogo' => Trustpilot::$plugin->profileService->getCompanyLogo($businessUnitId),
            'guaranteeBox' => Trustpilot::$plugin->profileService->getGuaranteeBox($businessUnitId),
            'profilePicture' => Trustpilot::$plugin->profileService->getProfileImage($businessUnitId),
            'promotion' => Trustpilot::$plugin->profileService->getProfilePromotion($businessUnitId),
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
     * Get logo for specific business unit
     * 
     * @param string $businessUnitId
     *
     * @return bool|string
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
     * @return bool|array
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
     * @return bool|string
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
     * @return bool|array
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