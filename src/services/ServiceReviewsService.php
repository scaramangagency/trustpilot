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
    public function deleteServiceReviewComment(string $reviewId) {
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
     * Gets the reviews's public information (stars, text, consumer, etc.)
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function getServiceReview(string $reviewId) {
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
    public function getServiceReviewLikes(string $reviewId) {
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
    public function getServiceReviewWebLinks(string $reviewId, string $locale = 'en-GB') {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/reviews/' . $reviewId . '/web-links');

        return $result->response;
    }

    /**
     * Gets the latest reviews written in a specfic language
     * @param string $language
     * @param string $locale
     * 
     * @return bool|JSON
     */
    public function getServiceReviews(string $language = 'en', string $locale = 'en-GB') {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/reviews/latest', array(
            'count' => 100,
            'locale' => $locale,
            'language' => $language
        ));

        return $result->response;
    }

    /**
     * Gets the a private reviews's public information (stars, text, consumer, etc.)
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function getPrivateServiceReview(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/private/reviews/' . $reviewId);

        return $result->response;
    }

    /**
     * Gets the reviews's public information (stars, text, consumer, etc.)
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function getServiceReviewTags(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/tags');

        return $result->response;
    }

    /**
     * Remove a tag from a service review
     * @param string $reviewId
     * 
     * @return bool|JSON
     */
    public function deleteServiceReviewTags(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->delete('https://api.trustpilot.com/v1/private/reviews/' . $reviewId . '/tags');

        return $result->response;
    }
}
