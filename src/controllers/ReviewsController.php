<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\controllers;

use scaramangagency\trustpilot\Trustpilot;
use scaramangagency\trustpilot\services\AuthenticationService;
use scaramangagency\trustpilot\services\ReviewsService;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;

class ReviewsController extends Controller
{
    // Public Methods
    // =========================================================================
    public function actionIndex($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }

        $page = Craft::$app->getRequest()->getQueryParam('page') ?? 1;

        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('trustpilot/settings/' . $siteId));
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);

        $data = [
            'getReviews' => Trustpilot::$plugin->reviewsService->getReviews($businessUnitId, $siteId, 12, $page),
            'businessUnit' => Trustpilot::$plugin->authenticationService->locateBusinessUnit(
                $trustpilotUrl,
                $siteId,
                true
            ),
            'siteId' => $siteId
        ];

        return $this->renderTemplate('trustpilot/reviews/index', $data);
    }
}
