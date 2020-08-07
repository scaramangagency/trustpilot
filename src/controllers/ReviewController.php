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
use scaramangagency\trustpilot\records\TrustpilotRecord as TrustpilotRecord;
use scaramangagency\trustpilot\services\AuthenticationService;
use scaramangagency\trustpilot\services\ReviewService;

use Craft;
use craft\web\Controller;

class ReviewController extends Controller
{

    // Public Methods
    // =========================================================================
    public function actionIndex($id) {
        $review = Trustpilot::$plugin->reviewService->getReview($id);

        $data = [
            'review' => $review
        ];

        return $this->renderTemplate('trustpilot/review/index', $data);
    }
}
