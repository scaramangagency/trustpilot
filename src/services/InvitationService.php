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
use craft\helpers\StringHelper;
use craft\services\Plugins;

use Curl\Curl;
use putyourlightson\logtofile\LogToFile;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class InvitationService extends Component
{
    // Public Methods
    // =========================================================================
    public function createInvitation($siteId, $businessUnitId, $serviceReviewInvitation, $consumer)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }

        $data = [
            'consumerEmail' => $consumer['email'],
            'consumerName' => $consumer['name'],
            'referenceNumber' => StringHelper::UUID(),
            'replyTo' => AuthenticationService::TRUSTPILOT_REPLY_TO($siteId),
            'senderEmail' => AuthenticationService::TRUSTPILOT_SENDER_EMAIL($siteId),
            'senderName' => AuthenticationService::TRUSTPILOT_SENDER_NAME($siteId),
            'serviceReviewInvitation' => $serviceReviewInvitation
        ];

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/email-invitations'
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        if (is_object(json_decode($result))) {
            return ['success' => false, 'message' => json_decode($result)->details];
        }

        return ['success' => true];
    }

    public function generateServiceReviewInvitationLink($siteId, $businessUnitId, $consumer)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }

        $site = Craft::$app->sites->getSiteById($siteId);
        $locale = $site->locale->id;

        $locales = Trustpilot::$plugin->resourcesService->getTrustpilotLocales($siteId)->locales;
        $key = array_search($locale, $locales) ?? array_search('en-US', $locales);

        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);
        $data = [
            'email' => $consumer['email'],
            'name' => $consumer['name'],
            'referenceId' => StringHelper::UUID(),
            'locale' => !$key ? 'en-US' : $locales[$key]->locale
        ];
        $result->post(
            'https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/invitation-links',
            $data
        );
        $result = $result->response;
        return $result;
    }

    public function getTemplateList($siteId)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);
        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }

        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);

        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);

        $result->get(
            'https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/templates'
        );

        $site = Craft::$app->sites->getSiteById($siteId);
        $locale = $site->locale->id;

        $result = json_decode($result->response)->templates;
        $key = array_search($locale, $result) ?? array_search('en-US', $result);

        return $result[$key];
    }
}
