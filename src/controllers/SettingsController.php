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

use Craft;
use craft\web\Controller;

class SettingsController extends Controller
{

    // Public Methods
    // =========================================================================
    public function actionIndex() {
        return $this->renderTemplate('trustpilot/settings', [
            'settings' => Trustpilot::$plugin->getSettings()
        ]);
    }

    public function actionSavePluginSettings() {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        $plugin = Craft::$app->getPlugins()->getPlugin('trustpilot');

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        // Save ID/Secret
        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
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
