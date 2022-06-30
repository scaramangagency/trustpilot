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

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class ReviewService extends Component
{
    // Public Methods
    // =========================================================================
    public function getReview($reviewId, $siteId)
    {
        $apiKey = Trustpilot::$plugin->authenticationService->getApiKey($siteId);

        if (!$apiKey) {
            Trustpilot::$plugin->log('Failed to retrieve API Key from database.');
            return false;
        }

        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/reviews/' . $reviewId, [
            'apikey' => $apiKey
        ]);

        return json_decode($result->response);
    }

    public function addComment($siteId, $reviewId, $comment)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);

        if (!$token) {
            Trustpilot::$plugin->log('Failed to retrieve get access token from database or Trustpilot.');
            return false;
        }
        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);
        $result->post('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/reply', [
            'message' => $comment
        ]);

        return $result->response;
    }

    public function deleteComment($siteId, $reviewId)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);

        if (!$token) {
            Trustpilot::$plugin->log('Failed to retrieve get access token from database or Trustpilot.');
            return false;
        }

        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);
        $result->delete('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/reply');

        return $result->response;
    }
}
