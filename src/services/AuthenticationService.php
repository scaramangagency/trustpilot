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
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use craft\services\Plugins;

use Curl\Curl;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class AuthenticationService extends Component
{
    public static function ACCESS_RECORD($siteId)
    {
        $params = [
            'siteId' => $siteId
        ];

        $accessRecord = TrustpilotRecord::findOne($params);

        if (!$accessRecord)
        {
            Trustpilot::$plugin->log('Trustpilot has not been set up for this site.');
            return false;
        }

        return $accessRecord;
    }

    public static function API_KEY($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['apiKey']);
    }
    public static function API_SECRET($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['apiSecret']);
    }
    public static function TRUSTPILOT_URL($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['trustpilotUrl']);
    }
    public static function TRUSTPILOT_USERNAME($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }
        
        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['trustpilotUsername']);
    }
    public static function TRUSTPILOT_PASSWORD($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['trustpilotPassword']);
    }
    public static function TRUSTPILOT_SENDER_EMAIL($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['invitationSenderEmail']);
    }
    public static function TRUSTPILOT_SENDER_NAME($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['invitationSenderName']);
    }
    public static function TRUSTPILOT_REPLY_TO($siteId)
    {
        if (!self::ACCESS_RECORD($siteId))
        {
            return null;
        }

        return Craft::parseEnv(self::ACCESS_RECORD($siteId)['invitationReplyToEmail']);
    }

    // Public Methods
    // =========================================================================
    public function getAccessToken($siteId)
    {
        $configRecord = self::ACCESS_RECORD($siteId);

        if (!$configRecord)
        {
            return Trustpilot::$plugin->authenticationService->setAccessToken($siteId);
        }

        $createdStamp = strtotime($configRecord->getAttribute('createdTimestamp'));
        $expiresIn = $configRecord->getAttribute('expiresIn');

        if ($createdStamp + $expiresIn < time())
        {
            return Trustpilot::$plugin->authenticationService->setAccessToken($siteId);
        }

        return $configRecord->getAttribute('accessToken');
    }

    public function getApiKey($siteId)
    {
        return self::API_KEY($siteId);
    }

    public function getTrustpilotUrl($siteId)
    {
        return self::TRUSTPILOT_URL($siteId);
    }

    public function setAccessToken($siteId)
    {
        $result = new Curl();
        $result->setHeader(
            'Authorization',
            'Basic ' . base64_encode(self::API_KEY($siteId) . ':' . self::API_SECRET($siteId))
        );
        $result->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $result->post('https://api.trustpilot.com/v1/oauth/oauth-business-users-for-applications/accesstoken', [
            'grant_type' => 'password',
            'username' => self::TRUSTPILOT_USERNAME($siteId),
            'password' => self::TRUSTPILOT_PASSWORD($siteId)
        ]);

        $payload = json_decode($result->response);

        if (isset($payload->reason)) {
            Trustpilot::$plugin->log('Failed to retrieve access token from Trustpilot.');
            return false;
        }

        return Trustpilot::$plugin->authenticationService->handlePayload($payload, $siteId);
    }

    public function returnBusinessUnitId($name, $siteId)
    {
        $reinit = false;

        $configRecord = self::ACCESS_RECORD($siteId);

        if ($configRecord) {
            $currentTrustpilotUrl = $configRecord->getAttribute('currentTrustpilotUrl') ?? null;
            $businessUnitId = $configRecord->getAttribute('businessUnitId') ?? null;

            if ($name != $currentTrustpilotUrl || is_null($businessUnitId)) {
                $reinit = true;
            }
        }

        if ($reinit) {
            return Trustpilot::$plugin->authenticationService->locateBusinessUnit($name, $siteId);
        }

        return $businessUnitId;
    }

    public function locateBusinessUnit($name, $siteId, $verbose = false)
    {
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/business-units/find', [
            'name' => $name,
            'apikey' => self::API_KEY($siteId)
        ]);

        $result = json_decode($result->response);

        if (!property_exists($result, 'displayName')) {
            Trustpilot::$plugin->log('Failed to get data from Trustpilot.');
            return false;
        }

        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin !== null) {
            if (!$verbose) {
                $configRecord = self::ACCESS_RECORD($siteId);

                $configRecord->setAttribute('currentTrustpilotUrl', $name);
                $configRecord->setAttribute('businessUnitId', $result->id);
                $configRecord->setAttribute('createdTimestamp', date('Y-m-d H:m:s', time()));
                $configRecord->setAttribute('dateCreated', date('Y-m-d H:m:s', time()));
                $configRecord->setAttribute('dateUpdated', date('Y-m-d H:m:s', time()));
                $configRecord->save();

                return $result->id;
            } else {
                return $result;
            }
        }

        return false;
    }

    // Private Methods
    // =========================================================================
    private function handlePayload($payload, $siteId)
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin !== null) {
            $configRecord = self::ACCESS_RECORD($siteId);

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
