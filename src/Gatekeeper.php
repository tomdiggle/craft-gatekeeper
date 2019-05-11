<?php
/**
 * Gatekeeper plugin for Craft CMS 3.x
 *
 * Protect your Craft CMS website from access with a universal password.
 *
 * @link      http://tomdiggle.com
 * @copyright Copyright (c) 2018 Tom Diggle
 */

namespace tomdiggle\gatekeeper;

use tomdiggle\gatekeeper\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\Application;
use craft\web\UrlManager;
use craft\web\Session;
use craft\web\View;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\web\Cookie;

/**
 * @author    Tom Diggle
 * @package   Gatekeeper
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Gatekeeper extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Gatekeeper
     */
    public static $plugin;

    /**
     * @var Settings
     */
    public static $settings;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;
        self::$settings = Gatekeeper::$plugin->getSettings();

        $this->registerEventListeners();

        Craft::info(
            Craft::t(
                'gatekeeper',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Craft::$app->getRequest()->getCookies()->get('gatekeeper') !== null;
    }

    // Protected Methods
    // =========================================================================

    /**
     *
     */
    protected function registerEventListeners()
    {
        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['gatekeeper'] = 'gatekeeper/gatekeeper';
                $event->rules['gatekeeper/login'] = 'gatekeeper/gatekeeper/login';
            }
        );

        // Handler: EVENT_AFTER_LOAD_PLUGINS
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_LOAD_PLUGINS,
            function () {
                // Only respond to non-console site requests
                $request = Craft::$app->getRequest();
                if ($request->getIsSiteRequest() && !$request->getIsConsoleRequest()) {
                    $this->handleSiteRequests();
                }
            }
        );
    }

    /**
     *
     */
    protected function handleSiteRequests()
    {
        // Handler: View::EVENT_BEFORE_RENDER_TEMPLATE
        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_TEMPLATE,
            function () {
                Craft::debug(
                    'View::EVENT_BEFORE_RENDER_TEMPLATE',
                    __METHOD__
                );

                if ($this->settings->enabled && $this->isGuest() && !$this->isAuthenticated() && !$this->isGatekeeperRequest()) {
                    $cookie = new Cookie(['name' => 'gatekeeper_referer']);
                    $cookie->value = Craft::$app->getRequest()->getUrl();
                    $cookie->expire = time() + 30;
                    Craft::$app->getResponse()->cookies->add($cookie);
                    Craft::$app->getResponse()->redirect('/gatekeeper');
                }
            }
        );
    }

    /**
     * @return bool
     */
    protected function isGuest(): bool
    {
        return Craft::$app->getUser()->getIsGuest();
    }

    /**
     * @return bool
     */
    protected function isGatekeeperRequest(): bool
    {
        $url = Craft::$app->getRequest()->getUrl();
        return stripos($url, 'gatekeeper');
    }

    /**
     * @param string $location
     * @return Response
     */
    public function redirectHelper(string $location): Response
    {
        if (strpos($location, '/') !== 0) {
            $location = '/' . $location;
        }
        $currentSite = Craft::$app->getSites()->getCurrentSite();
        if ($currentSite->baseUrl) {
            $baseUrl = Craft::getAlias($currentSite->baseUrl);
            return Craft::$app->getResponse()->redirect(rtrim($baseUrl, '/') . $location);
        }
        return Craft::$app->getResponse()->redirect($location);
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        // Get the settings that are being defined by the config file
        $overrides = Craft::$app->getConfig()->getConfigFromFile('gatekeeper');

        return Craft::$app->view->renderTemplate(
            'gatekeeper/settings',
            [
                'settings' => $this->getSettings(),
                'overrides' => array_keys($overrides),
            ]
        );
    }
}
