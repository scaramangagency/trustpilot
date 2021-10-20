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
use scaramangagency\trustpilot\models\Invitation;
use scaramangagency\trustpilot\models\ReviewLink;
use scaramangagency\trustpilot\services\AuthenticationService;
use scaramangagency\trustpilot\services\InvitationService;
use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;

class InvitationsController extends Controller
{
    // Public Methods
    // =========================================================================
    public function actionIndex($siteId = 0, Invitation $invitation = null, ReviewLink $link = null)
    {
        if ($siteId == 0) {
            $siteId = Craft::$app->sites->primarySite->id;
        }

        $data = [
            'siteId' => $siteId,
            'templateList' => Trustpilot::$plugin->invitationService->getTemplateList($siteId)
        ];

        if (!$invitation) {
            $invitation = new Invitation();
        }

        if (!$link) {
            $link = new ReviewLink();
        }

        return $this->renderTemplate('trustpilot/invitations/index', [
            'data' => $data,
            'siteId' => $siteId,
            'invitation' => $invitation,
            'link' => $link,
            'reviewLink' => null
        ]);
    }

    public function actionCreateInvitation()
    {
        $data = Craft::$app->getRequest()->getBodyParam('invitation', []);

        $model = new Invitation();
        $model->email = $data['email'];
        $model->name = $data['name'];
        $model->templateId = $data['templateId'];
        $model->siteId = $data['siteId'];
        $model->redirectUri = $data['redirectUri'];

        if (!$model->validate()) {
            Craft::$app->getUrlManager()->setRouteParams([
                'invitation' => $model
            ]);

            return null;
        }

        $data['templateList'] = Trustpilot::$plugin->invitationService->getTemplateList($data['siteId']);

        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($data['siteId']);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId(
            $trustpilotUrl,
            $data['siteId']
        );

        $consumer = [
            'name' => $data['name'],
            'email' => $data['email']
        ];

        $review = [
            'templateId' => $data['templateId'],
            'redirectUri' =>
                $data['redirectUri'] != ''
                    ? $data['redirectUri']
                    : Trustpilot::$plugin->profileService->getTrustpilotPermalink($businessUnitId, $data['siteId'])[
                        'profileUrl'
                    ],
            'preferredSendTime' => date('c', strtotime('+1 hours'))
        ];

        $queueInvite = Trustpilot::$plugin->invitationService->createInvitation(
            $data['siteId'],
            $businessUnitId,
            $review,
            $consumer
        );

        $invitation = new Invitation();
        $link = new ReviewLink();

        if (!$queueInvite['success']) {
            Craft::$app->getSession()->setError('Failed to queue invitation. ' . $queueInvite['message']);
        } else {
            Craft::$app->getSession()->setNotice('Successfully queued invitation.');
        }

        return $this->renderTemplate('trustpilot/invitations/index', [
            'data' => $data,
            'siteId' => $data['siteId'],
            'invitation' => $invitation,
            'link' => $link,
            'reviewLink' => null
        ]);
    }

    public function actionGetServiceReviewLink()
    {
        $data = Craft::$app->getRequest()->getBodyParam('link', []);

        $model = new ReviewLink();
        $model->email = $data['email'];
        $model->name = $data['name'];

        if (!$model->validate()) {
            Craft::$app->getUrlManager()->setRouteParams([
                'link' => $model
            ]);

            return null;
        }

        $trustpilotUrl = Trustpilot::$plugin->authenticationService->getTrustpilotUrl($data['siteId']);
        $businessUnitId = Trustpilot::$plugin->authenticationService->returnBusinessUnitId(
            $trustpilotUrl,
            $data['siteId']
        );

        $consumer = [
            'name' => $data['name'],
            'email' => $data['email']
        ];

        $reviewLinkFromAPI = Trustpilot::$plugin->invitationService->generateServiceReviewInvitationLink(
            $data['siteId'],
            $businessUnitId,
            $consumer
        );

        $data['templateList'] = Trustpilot::$plugin->invitationService->getTemplateList($data['siteId']);

        $invitation = new Invitation();
        $link = new ReviewLink();

        return $this->renderTemplate('trustpilot/invitations/index', [
            'data' => $data,
            'siteId' => $data['siteId'],
            'invitation' => $invitation,
            'link' => $link,
            'reviewLink' => json_decode($reviewLinkFromAPI)
        ]);
    }
}
