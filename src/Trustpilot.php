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

        $this->setComponents([
            'authenticationService' => \scaramangagency\trustpilot\services\AuthenticationService::class,
            'profileService' => \scaramangagency\trustpilot\services\ProfileService::class,
            'reviewsService' => \scaramangagency\trustpilot\services\ReviewsService::class,
            'reviewService' => \scaramangagency\trustpilot\services\ReviewService::class,
            'resourcesService' => \scaramangagency\trustpilot\services\ResourcesService::class
        ]);

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
                    'trustpilot/reviews/' => 'trustpilot/reviews/index',
                    'trustpilot/reviews/profile' => 'trustpilot/reviews/profile',
                    'trustpilot/reviews/resources' => 'trustpilot/reviews/resources',
                    'trustpilot/reviews/invitation' => 'trustpilot/reviews/invitation',
                    'trustpilot/reviews/service-reviews' => 'trustpilot/reviews/service-reviews',
                    'trustpilot/reviews/<id:[^/]+>' => 'trustpilot/review/index'
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
