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

use Craft;
use craft\web\Controller;

class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================
    public function actionIndex($siteId = 0)
    {
        if ($siteId == 0) {
            $settingsRecord = TrustpilotRecord::findOne(1);
            $siteId = Craft::$app->sites->primarySite->id;
        } else {
            $params = [
                'siteId' => $siteId
            ];

            $settingsRecord = TrustpilotRecord::findOne($params);
        }

        if (!$settingsRecord) {
            $settingsRecord = new TrustpilotRecord();
        }

        return $this->renderTemplate('trustpilot/settings', [
            'siteId' => $siteId,
            'settings' => $settingsRecord
        ]);
    }

    public function actionSavePluginSettings()
    {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        $params = [
            'siteId' => $settings['siteId']
        ];

        $settingsRecord = TrustpilotRecord::findOne($params);

        if (!$settingsRecord) {
            $settingsRecord = new TrustpilotRecord();
        }

        $settingsRecord->setAttribute('apiKey', $settings['apiKey']);
        $settingsRecord->setAttribute('siteId', $settings['siteId']);
        $settingsRecord->setAttribute('apiSecret', $settings['apiSecret']);
        $settingsRecord->setAttribute('trustpilotUsername', $settings['trustpilotUsername']);
        $settingsRecord->setAttribute('trustpilotPassword', $settings['trustpilotPassword']);
        $settingsRecord->setAttribute('invitationSenderEmail', $settings['invitationSenderEmail']);
        $settingsRecord->setAttribute('invitationSenderName', $settings['invitationSenderName']);
        $settingsRecord->setAttribute('invitationReplyToEmail', $settings['invitationReplyToEmail']);
        $settingsRecord->setAttribute('trustpilotUrl', $settings['trustpilotUrl']);

        if (!$settingsRecord->save()) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));

            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }
}
