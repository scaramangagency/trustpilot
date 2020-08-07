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
use scaramangagency\trustpilot\services\ReviewsService;
use scaramangagency\trustpilot\services\ProfileService;
use scaramangagency\trustpilot\services\ResourcesService;

use Craft;
use craft\web\Controller;

class ReviewsController extends Controller
{

    // Public Methods
    // =========================================================================
    public function actionIndex() {
        $trustpilotUrl = Craft::parseEnv(Trustpilot::$plugin->getSettings()->trustpilotUrl);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnit($trustpilotUrl);

        $data = [
            'getReviews' => Trustpilot::$plugin->reviewsService->getReviews($businessUnitId),
        ];

        return $this->renderTemplate('trustpilot/reviews/index', $data);
    }

    public function actionProfile() {
        $trustpilotUrl = Craft::parseEnv(Trustpilot::$plugin->getSettings()->trustpilotUrl);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnit($trustpilotUrl);

        $data = [
            'getProfile' => Trustpilot::$plugin->profileService->getProfile($businessUnitId),
        ];

        return $this->renderTemplate('trustpilot/reviews/_profile', $data);
    }

    public function actionInvitation() {
        return $this->renderTemplate('trustpilot/reviews/_invitation');
    }

    public function actionResources() {
        $trustpilotUrl = Craft::parseEnv(Trustpilot::$plugin->getSettings()->trustpilotUrl);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnit($trustpilotUrl);
        $profile = Trustpilot::$plugin->profileService->getProfile($businessUnitId);
 
        $data = [
            'stars' => Trustpilot::$plugin->resourcesService->getTrustpilotStarImage($profile['stars']),
            'verbose' => Trustpilot::$plugin->resourcesService->getTrustpilotStarVerbose($profile['stars']),
            'icons' =>Trustpilot::$plugin->resourcesService->getTrustpilotIcons(),
            'logos' =>Trustpilot::$plugin->resourcesService->getTrustpilotLogos()
        ];

        return $this->renderTemplate('trustpilot/reviews/_resources', $data);
    }

    public function actionServiceReviews() {
        return $this->renderTemplate('trustpilot/reviews/_service-reviews');
    }
}
