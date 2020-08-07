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
class AuthenticationService extends Component
{
    public static function API_KEY() { return Craft::parseEnv(Trustpilot::$plugin->getSettings()->apiKey); }
    public static function API_SECRET() { return Craft::parseEnv(Trustpilot::$plugin->getSettings()->apiSecret); }
    public static function TRUSTPILOT_USERNAME() { return Craft::parseEnv(Trustpilot::$plugin->getSettings()->trustpilotUsername); }
    public static function TRUSTPILOT_PASSWORD() { return Craft::parseEnv(Trustpilot::$plugin->getSettings()->trustpilotPassword); }

    // Public Methods
    // =========================================================================

    /**
     * Returns the access token for OAuth requests. Will generate key if it doesn't exist, or refresh if it has expired.
     *
     * @return string
     */
    public function getAccessToken() {
        $configRecord = TrustpilotRecord::findOne(1); 
        $createdStamp = strtotime($configRecord->getAttribute('createdTimestamp'));
        $expiresIn = $configRecord->getAttribute('expiresIn');

        if (!$configRecord || ($createdStamp + $expiresIn) < time()) {
            return Trustpilot::$plugin->authenticationService->setAccessToken();
        }

        return $configRecord->getAttribute('accessToken');
    }

    /**
     * Returns the API Key defined in the settings
     *
     * @return string
     */
    public function getApiKey() {
        return self::API_KEY();
    }

    /**
     * Grabs the access token for OAuth requests from Trustpilot
     *
     * @return bool|string
     */
    public function setAccessToken() {
        $result = new Curl();
        $result->setHeader('Authorization', 'Basic ' . base64_encode(self::API_KEY() . ':' . self::API_SECRET()));
        $result->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $result->post('https://api.trustpilot.com/v1/oauth/oauth-business-users-for-applications/accesstoken', array(
            'grant_type' => 'password',
            'username' => self::TRUSTPILOT_USERNAME(),
            'password' => self::TRUSTPILOT_PASSWORD()
        ));

        $payload = json_decode($result->response);
        
        if (isset($payload->reason)) {
            LogToFile::info('Failed to retrieve access token from Trustpilot', 'Trustpilot');
            return false;
        }

        return Trustpilot::$plugin->authenticationService->handlePayload($payload);
    }

    /**
     * Refreshes the access token
     *
     * @return bool|string
     */
    public function refreshAccessToken() {
        $result = new Curl();
        $result->setHeader('Authorization', 'Basic ' . base64_encode(self::API_KEY() . ':' . self::API_SECRET()));
        $result->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $result->post('https://api.trustpilot.com/v1/oauth/oauth-business-users-for-applications/refresh', array(
            'grant_type' => 'refresh_token',
            'refresh_token' => Trustpilot::$plugin->authenticationService->getRefreshToken()
        ));

        $payload = json_decode($result->response);

        if (isset($payload->reason)) {
            LogToFile::info('Failed to refresh access token ', 'Trustpilot');
            return false;
        }

        return Trustpilot::$plugin->authenticationService->handlePayload($payload);
    }


    /**
     * Returns the business unit if it exists in the database, or grabs it from the API if not
     * 
     * @param string $name
     *
     * @return bool|string
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

    /**
     * Find a business unit
     * 
     * @param string $name
     *
     * @return bool|string
     */
    public function locateBusinessUnit(string $name) {
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/find', array(
            'name' => $name,
            'apikey' => $self::API_KEY()
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


    // Private Methods
    // =========================================================================
    /**
     * Grab the refersh token from the database.
     *
     * @return bool|string
     */
    private function getRefreshToken() {
        $configRecord = TrustpilotRecord::findOne(1); 

        if (!$configRecord) {
            LogToFile::info('An access token has not been obtained from Trustpilot', 'Trustpilot');
            return false;
        }

        return $configRecord->getAttribute('refreshToken');
    }

    /**
     * Save the Trustpilot OAuth response to the database
     *
     * @return bool|string
     */
    private function handlePayload($payload) {
        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin !== null) {
            $configRecord = TrustpilotRecord::findOne(1);
            
            if (!$configRecord) {
                $configRecord = new TrustpilotRecord();
            }

            $configRecord->setAttribute('accessToken', $payload->access_token);
            $configRecord->setAttribute('refreshToken', $payload->refresh_token);
            $configRecord->setAttribute('expiresIn', $payload->expires_in);
            $configRecord->setAttribute('createdTimestamp', date('Y-m-d H:m:s', time()));
            $configRecord->setAttribute('dateUpdated', date('Y-m-d H:m:s', time()));
            $configRecord->save();

            return $payload->access_token;
        }

        return false;
    }
}
