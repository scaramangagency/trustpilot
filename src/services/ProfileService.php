<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\services;

use scaramangagency\trustpilot\Trustpilot;
use scaramangagency\trustpilot\services\AuthenticationService;
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;

use Craft;
use craft\base\Component;
use craft\services\Plugins;

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
    public function getProfile($businessUnitId, $siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            Trustpilot::$plugin->log('Failed to retrieve API Key from database');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId, [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        if (!property_exists($result, 'displayName')) {
            Trustpilot::$plugin->log('Failed to get data from Trustpilot.');
            return false;
        }

        $data = [
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

    public function getTrustpilotPermalink($businessUnitId, $siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            Trustpilot::$plugin->log('Failed to retrieve API Key from database.');
            return false;
        }

        $site = Craft::$app->sites->getSiteById($siteId);
        $locale = $site->locale->id;

        $locales = Trustpilot::$plugin->resourcesService->getTrustpilotLocales($siteId)->locales;
        $key = array_search($locale, $locales) ?? array_search('en-US', $locales);

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/' . $businessUnitId . '/web-links', [
            'apikey' => $apiKey,
            'locale' => !$key ? 'en-US' : $locales[$key]->locale
        ]);

        $result = json_decode($result->response);

        if (!property_exists($result, 'profileUrl')) {
            Trustpilot::$plugin->log('Failed to retrieve data from Trustpilot.');
            return false;
        }

        $data = [
            'profileUrl' => $result->profileUrl,
            'evaluateEmbedUrl' => $result->evaluateEmbedUrl,
            'evaluateUrl' => $result->evaluateUrl
        ];

        return $data;
    }
}
