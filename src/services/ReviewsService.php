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
use putyourlightson\logtofile\LogToFile;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class ReviewsService extends Component
{
    // Public Methods
    // =========================================================================
    public function getReviews($businessUnitId, $siteId, $limit = 12, $page = 1)
    {
        $token = Trustpilot::$plugin->authenticationService->getAccessToken($siteId);

        if (!$token) {
            LogToFile::info('Failed to retrieve get access token from database or Trustpilot', 'Trustpilot');
            return false;
        }

        $result = new Curl();
        $result->setHeader('Authorization', 'Bearer ' . $token);
        $result->get('https://api.trustpilot.com/v1/private/business-units/' . $businessUnitId . '/reviews', [
            'page' => $page,
            'perPage' => $limit
        ]);

        $result = json_decode($result->response);

        if (!property_exists($result, 'reviews')) {
            LogToFile::info('Failed to get data from Trustpilot. Result: ' . json_encode($result), 'Trustpilot');
            Craft::$app
                ->getSession()
                ->setError(Craft::t('app', 'Unable to connect to Trustpilot. Please check your settings.'));
            return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('trustpilot/settings/' . $siteId));
        }

        return $result;
    }
}
