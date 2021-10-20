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
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;
use scaramangagency\trustpilot\services\AuthenticationService;
use scaramangagency\trustpilot\services\ReviewService;

use Craft;
use craft\web\Controller;

class ReviewController extends Controller
{
    public function actionAddReviewComment()
    {
        $this->requirePostRequest();
        $data = Craft::$app->getRequest()->getBodyParam('review', []);
        $review = Trustpilot::$plugin->reviewService->addComment($data['siteId'], $data['reviewId'], $data['comment']);

        return $this->redirect(Craft::$app->getRequest()->referrer);
    }

    public function actionDeleteReviewComment($siteId, $reviewId)
    {
        $review = Trustpilot::$plugin->reviewService->deleteComment($siteId, $reviewId);

        return $this->redirect(Craft::$app->getRequest()->referrer);
    }
}
