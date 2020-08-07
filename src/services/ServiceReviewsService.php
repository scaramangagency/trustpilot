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

use Craft;
use craft\base\Component;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class ServiceReviewsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Delete a reply to a review
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function deleteComment(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->delete('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/reply');

        return $result->response;
    }

    /**
     * Prepare review for conversation
     * @param string $reviewId
     * 
     * @return bool|string
     */
    public function enableConversation(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->post('https://api.trustpilot.com/v1/private/product-reviews/' . $reviewId . '/create-conversation');
        $result = json_decode($result->response);
        
        return $result->conversationId;
    }

    /**
     * Add comment to review
     * @param string $conversationId
     * 
     * @return bool|string
     */
    public function addComment(string $conversationId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->post('https://api.trustpilot.com/v1/private/conversations/' . $conversationId . '/comments');
        $result = json_decode($result->response);
        
        return $result->content;
    }

    /**
     * Gets the reviews's public information (stars, text, consumer, etc.)
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function getReview(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/reply');

        return $result->response;
    }

    /**
     * Gets the reviews's public information(stars, text, consumer, etc.)
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function getLikes(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/reviews/' . $reviewId . '/likes');

        return $result->response;
    }

    /**
     * Gets links to a review's public page on Trustpilot
     * @param string $reviewId
     * @param string $locale
     * 
     * @return bool|JSON
     */
    public function getLink(string $reviewId, string $locale = 'en-GB') {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/reviews/' . $reviewId . '/web-links');

        return $result->response;
    }
}
