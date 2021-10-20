<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\variables;

use scaramangagency\trustpilot\Trustpilot;

use Craft;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class TrustpilotVariable
{
    // Public Methods
    // =========================================================================
    public function getAllReviews($page, $limit, $siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }
        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return false;
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);
        $raw = Trustpilot::$plugin->reviewsService->getReviews($businessUnitId, $siteId, $limit, $page);

        $reviews = [];

        foreach ($raw->reviews as $key => $review) {
            $reviews[] = [
                'author' => [
                    'name' => $review->consumer->displayName,
                    'previousReviewCount' => $review->consumer->numberOfReviews
                ],
                'reviewStars' => [
                    'count' => $review->stars,
                    'image' => $review->links[2]->href
                ],
                'reviewTitle' => $review->title,
                'reviewText' => $review->text,
                'reviewPosted' => $review->createdAt,
                'companyReply' => $review->companyReply->text ?? ''
            ];
        }
        return $reviews;
    }

    public function getProfile($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }
        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return false;
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);
        return Trustpilot::$plugin->profileService->getProfile($businessUnitId, $siteId);
    }

    public function getTrustpilotStarImage($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }
        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return false;
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);
        $profile = Trustpilot::$plugin->profileService->getProfile($businessUnitId, $siteId);

        return Trustpilot::$plugin->resourcesService->getTrustpilotStarImage($profile['stars'], $siteId);
    }

    public function getTrustpilotPermalinks($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }
        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return false;
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);
        return Trustpilot::$plugin->profileService->getTrustpilotPermalink($businessUnitId, $siteId);
    }

    public function getTrustpilotStarText($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }
        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($siteId);

        if (!$trustpilotUrl) {
            return false;
        }

        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId($trustpilotUrl, $siteId);
        $profile = Trustpilot::$plugin->profileService->getProfile($businessUnitId, $siteId);

        return Trustpilot::$plugin->resourcesService->getTrustpilotStarVerbose($profile['stars'], $siteId);
    }

    public function getTrustpilotIcons($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }

        return Trustpilot::$plugin->resourcesService->getTrustpilotIcons($siteId);
    }

    public function getTrustpilotLogos($siteId = 0)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }

        return Trustpilot::$plugin->resourcesService->getTrustpilotLogos($siteId);
    }
}
