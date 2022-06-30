<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
 */

namespace scaramangagency\trustpilot;

use scaramangagency\trustpilot\variables\TrustpilotVariable;
use scaramangagency\trustpilot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

use craft\log\MonologTarget;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;
use yii\log\Logger;

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
    public static $plugin;

    // Public Properties
    // =========================================================================
    public string $schemaVersion = '2.0.0';

    public bool $hasCpSettings = true;

    public bool $hasCpSection = true;

    // Public Methods
    // =========================================================================
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->registerCpUrls();
        $this->registerPermissions();

        $this->setComponents([
            'authenticationService' => \scaramangagency\trustpilot\services\AuthenticationService::class,
            'invitationService' => \scaramangagency\trustpilot\services\InvitationService::class,
            'profileService' => \scaramangagency\trustpilot\services\ProfileService::class,
            'resourcesService' => \scaramangagency\trustpilot\services\ResourcesService::class,
            'reviewService' => \scaramangagency\trustpilot\services\ReviewService::class,
            'reviewsService' => \scaramangagency\trustpilot\services\ReviewsService::class
        ]);

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $variable = $event->sender;
            $variable->set('trustpilot', TrustpilotVariable::class);
        });

        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin === $this) {
                $request = Craft::$app->getRequest();
                if ($request->isCpRequest) {
                    Craft::$app
                        ->getResponse()
                        ->redirect(UrlHelper::cpUrl('trustpilot/settings'))
                        ->send();
                }
            }
        });

        Craft::info(Craft::t('trustpilot', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);

        $this->_registerLogTarget();
    }

    /**
     * Logs a message
     */
    public function log(string $message, int $type = Logger::LEVEL_INFO): void
    {
        Craft::getLogger()->log($message, $type, 'trustpilot');
    }

    public function getCpNavItem(): ?array
    {
        $cpNav = parent::getCpNavItem();
        $subNavs = [];
        $request = Craft::$app->getRequest();

        $user = Craft::$app->getUser()->getIdentity();

        if ($user->can('trustpilot:reviews')) {
            $subNavs['reviews'] = ['label' => 'Reviews', 'url' => 'trustpilot/reviews'];
        }

        if ($user->can('trustpilot:invitations')) {
            $subNavs['invitations'] = ['label' => 'Invitations', 'url' => 'trustpilot/invitations'];
        }

        if ($user->can('trustpilot:settings') && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $subNavs['settings'] = ['label' => 'Settings', 'url' => 'trustpilot/settings'];
        }

        $cpNav = array_merge($cpNav, [
            'subnav' => $subNavs
        ]);

        return $cpNav;
    }

    public function getSettingsResponse(): mixed
    {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('trustpilot/settings'));
    }

    // Private Methods
    // =========================================================================
    private function registerCpUrls()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'trustpilot/settings' => 'trustpilot/settings/index',
                'trustpilot/reviews/' => 'trustpilot/reviews/index',
                'trustpilot/invitations' => 'trustpilot/invitations/index',
                'trustpilot/invitations/create-invitation' => 'trustpilot/invitations/create-invitation',

                'trustpilot/settings/<siteId>' => 'trustpilot/settings/index',
                'trustpilot/reviews/<siteId>' => 'trustpilot/reviews/index',
                'trustpilot/invitations/<siteId>' => 'trustpilot/invitations/index',

                'trustpilot/review/delete-comment/<siteId>/<reviewId:[^/]+>' =>
                    'trustpilot/review/delete-review-comment'
            ]);
        });
    }

    private function registerPermissions()
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function (
            RegisterUserPermissionsEvent $event
        ) {
            $event->permissions['trustpilot'] = [
                'trustpilot:settings' => [
                    'label' => 'Settings'
                ],
                'trustpilot:reviews' => [
                    'label' => 'View reviews',
                    'nested' => [
                        'trustpilot:reviews:comments' => [
                            'label' => 'Manage comments'
                        ]
                    ]
                ],
                'trustpilot:invitations' => [
                    'label' => 'Send invitations'
                ]
            ];
        });
    }

    /**
     * Registers a custom log target, keeping the format as simple as possible.
     *
     * @see LineFormatter::SIMPLE_FORMAT
     */
    private function _registerLogTarget(): void
    {
        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'trustpilot',
            'categories' => ['trustpilot'],
            'level' => LogLevel::INFO,
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "[%datetime%] %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new Settings();
    }
}
