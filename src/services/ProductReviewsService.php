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
use craft\services\Plugins;
use putyourlightson\logtofile\LogToFile;
use Curl\Curl;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class ProductReviewsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Add comment to product review
     * 
     * @param string $consumerId
     * @param string $content
     *
     * @return bool|JSON
     */
    public function addCommentToProductReview(string $conversationId, string $content) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->post('https://api.trustpilot.com/v1/private/conversations/' . $conversationId . '/comments', array(
            'content' => $content
        ));

        $result = $result->response;
        
        if (!isset($result->content)) {
            LogToFile::info('Failed to get save comment on Trustpilot', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Prepare review to accept comments
     * 
     * @param string $reviewId
     *
     * @return bool|JSON
     */
    public function prepareProductReviewForConversation(string $reviewId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->post('https://api.trustpilot.com/v1/private/product-reviews/' . $reviewId . '/create-conversation');

        $result = $result->response;
        
        if (!isset($result->conversationId)) {
            LogToFile::info('Failed to get prepare review for conversations', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get invitation link for product.
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
     * @param array $consumer An array of consumer information
     *              { "consumerEmail": "joe.bloggs@example.com",
     *                "consumerName": "Joe Bloggs" }
     * 
     * @return bool|JSON
     */
    public function generateProductReviewInvitationLink(string $businessUnitId, array $products, array $consumer, string $redirectUri) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();

        $data = array(
            'consumer' => array(
                'email' => $consumer->email,
                'name' => $consumer->name,
            ),
            'products' => $products,
            'referenceNumber' => StringHelper::UUID(),
            'redirectUri' => $redirectUri
        );

        $result->post('https://api.trustpilot.com/v1/private/product-reviews/business-units/' . $businessUnitId .'/invitation-links');

        $result = $result->response;
        
        if (!isset($result->reviewUrl)) {
            LogToFile::info('Failed to get product review invitation link', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get a single comment by id
     * 
     * @param string $conversationId
     * @param string $commentId
     *
     * @return bool|JSON
     */
    public function getProductReviewCommentById(string $conversationId, string $commentId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/private/conversations/' . $conversationId . '/comments/' . $commentId);

        $result = $result->response;
        
        if (!isset($result->content)) {
            LogToFile::info('Failed to get comment', 'Trustpilot');
            return false;
        }

        return $result;
    }

    /**
     * Get a conversation by id
     * 
     * @param string $conversationId
     *
     * @return bool|JSON
     */
    public function getProductReviewConversationById(string $conversationId) {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken();

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }
        
        $result = new Curl();
        $result->get('https://api.trustpilot.com/v1/private/conversations/' . $conversationId);

        $result = $result->response;
        
        if (!isset($result->comments)) {
            LogToFile::info('Failed to get conversation', 'Trustpilot');
            return false;
        }

        return $result;
    }
}
