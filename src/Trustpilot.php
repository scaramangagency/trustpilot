<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
 */

namespace scaramangagency\trustpilot;

use scaramangagency\trustpilot\services\TrustpilotService as TrustpilotServiceService;
use scaramangagency\trustpilot\variables\TrustpilotVariable;
use scaramangagency\trustpilot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Trustpilot
 *
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 *
 * @property  TrustpilotServiceService $trustpilotService
 */
class Trustpilot extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Trustpilot
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

     /**
     * @inheritdoc
     */
    public function getSettingsResponse() {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('trustpilot/settings'));
    }
    
    /**
     * @inheritdoc
     */
    public function getCpNavItem() {
        $navItems = array_merge(parent::getCpNavItem(), [
            'subnav' => [
                'reviews' => ['label' => 'Reviews', 'url' => 'trustpilot/reviews'],
                'settings' => ['label' => 'Settings', 'url' => 'trustpilot/settings']
            ]
        ]);

        return $navItems;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('trustpilot', TrustpilotVariable::class);
            }
        );

        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules = array_merge($event->rules, [
                    'trustpilot/settings' => 'trustpilot/settings/index',
                    'trustpilot/reviews/business' => 'trustpilot/reviews/business',
                    'trustpilot/reviews/categories' => 'trustpilot/reviews/categories',
                    'trustpilot/reviews/consumer' => 'trustpilot/reviews/consumer',
                    'trustpilot/reviews/consumer-profile' => 'trustpilot/reviews/consumer-profile',
                    'trustpilot/reviews/invitation' => 'trustpilot/reviews/invitation',
                    'trustpilot/reviews/resources' => 'trustpilot/reviews/resources',
                    'trustpilot/reviews/service-reviews' => 'trustpilot/reviews/service-reviews'
                ]);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    $request = Craft::$app->getRequest();
                    if ($request->isCpRequest) {
                        //
                    }
                }
            }
        );

        Craft::info(
            Craft::t(
                'trustpilot',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}
