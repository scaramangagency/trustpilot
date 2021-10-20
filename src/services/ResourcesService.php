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

use Craft;
use craft\base\Component;
use craft\services\Plugins;

use Curl\Curl;
use putyourlightson\logtofile\LogToFile;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class ResourcesService extends Component
{
    // Public Methods
    // =========================================================================
    public function getTrustpilotStarImage($score, $siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/stars/' . $score, [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        return $result;
    }

    public function getTrustpilotStarVerbose($score, $siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/strings/stars/' . $score, [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        return $result;
    }

    public function getTrustpilotIcons($siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/icons', [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        return $result;
    }

    public function getTrustpilotLogos($siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/images/logos', [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        return $result;
    }

    public function getTrustpilotLocales($siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            LogToFile::info('Failed to retrieve API Key from database', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/resources/metadata/locales', [
            'apikey' => $apiKey
        ]);

        $result = json_decode($result->response);

        return $result;
    }
}
