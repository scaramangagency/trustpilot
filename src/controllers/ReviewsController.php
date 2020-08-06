<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\controllers;

use scaramangagency\trustpilot\Trustpilot;
use scaramangagency\trustpilot\services\TrustpilotService;
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;

use Craft;
use craft\web\Controller;

class ReviewsController extends Controller
{

    // Public Methods
    // =========================================================================
    public function actionBusiness() {
        return $this->renderTemplate('trustpilot/reviews/_business');
    }
    public function actionCategories() {
        return $this->renderTemplate('trustpilot/reviews/_categories');
    }
    public function actionConsumer() {
        return $this->renderTemplate('trustpilot/reviews/_consumer');
    }
    public function actionConsumerProfile() {
        return $this->renderTemplate('trustpilot/reviews/_consumer-profile');
    }
    public function actionInvitation() {
        return $this->renderTemplate('trustpilot/reviews/_invitation');
    }
    public function actionResources() {
        return $this->renderTemplate('trustpilot/reviews/_resources');
    }
    public function actionServiceReviews() {
        return $this->renderTemplate('trustpilot/reviews/_service-reviews');
    }
}
