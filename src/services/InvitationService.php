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
use craft\helpers\StringHelper;
use craft\services\Plugins;
use putyourlightson\logtofile\LogToFile;
use Curl\Curl;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class InvitationService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Triggers email invitation(s).
     * 
     * @param string $businessUnitId
     * 
     * @param array $products An array of product data
     *              { "preferredSendTime": "2013-09-07T13:37:00",
     *                "redirectUri": "http://trustpilot.com",
     *                "templateId": "507f191e810c19729de860ea"
     *                products: { 
     *                  "sku": "ABC-1234", 
     *                  "name": "Metal Toy Car", 
     *                  "productUrl": "http://www.mycompanystore.com/products/12345.htm", 
     *                  "imageUrl": "http://www.mycompanystore.com/products/images/12345.jpg" 
     *                }
     *              }
     * 
     * @param array $serviceReviewInvitation An array for service reviewing
     *              { "preferredSendTime": "2013-09-07T13:37:00",
     *                "redirectUri": "http://trustpilot.com"
     *                "templateId": "507f191e810c19729de860ea" }
     * 
     * @param array $consumer An array of consumer information
     *              { "consumerEmail": "joe.bloggs@example.com",
     *                "consumerName": "Joe Bloggs" }
     * 
     * @return bool
     */
    public function createInvitation(string $businessUnitId, array $products, array $serviceReviewInvitation, array $consumer) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();

        $data = array(
            'consumerEmail' => $consumer->email,
            'consumerName' => $consumer->name,
            'referenceNumber' => StringHelper::UUID(),
            'replyTo' => Craft::parseEnv(Trustpilot::$plugin->getSettings()->invitationReplyToEmail),
            'senderEmail' => Craft::parseEnv(Trustpilot::$plugin->getSettings()->invitationSenderEmail),
            'senderName' => Craft::parseEnv(Trustpilot::$plugin->getSettings()->invitationSenderName),
            'serviceReviewInvitation' => $serviceReviewInvitation
        );
        
        if (Craft::parseEnv(Trustpilot::$plugin->getSettings()->sendCommerceInvitations)) {
            array_push($data, $products); 
        }

        return $result->post('https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/email-invitation', $data);
    }

    /**
     * Delete invitation data.
     * 
     * @param string $businessUnitId
     * @param date $date
     * @param array $emails
     * 
     * @return bool
     */
    public function deleteInvitationData(string $businessUnitId, timestamp $date, array $emails) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();

        if ($emails) {
            $data = array(
                'customerEmails' => $emails,
            );
        } else {
            $data = array(
                'deleteOlderThan' => $date
            );
        }

        return $result->post('https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/invitation-data/delete', $data);
    }

    /**
     * Generate a unique invitation link that can be sent to a consumer by email or website.
     * 
     * @param string $businessUnitId
     * @param array $consumer An array of consumer information
     *              { "consumerEmail": "joe.bloggs@example.com",
     *                "consumerName": "Joe Bloggs" }
     * @param string $redirectUri
     * 
     * @return bool|string
     */
    public function generateServiceReviewInvitationLink(string $businessUnitId, array $consumer, string $redirectUri) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();

        $data = array(
            'email' => $consumer->email,
            'name' => $consumer->name,
            'referenceId' => StringHelper::UUID(),
            'redirectUri' => $redirectUri
        );
        
        $result->post('https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/invitation-links', $data);
        $result = $result->response;

        if (!isset($result->url)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Returns a list of ID and Names of the templates available to be used in invitations
     * 
     * @param string $businessUnitId
     * 
     * @return bool|string
     */
    public function getInvitationTemplateList(string $businessUnitId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->post('https://invitations-api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/invitation-links');
        $result = $result->response;

        if (!isset($result->templates)) {
            LogToFile::info('Failed to get data from Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
