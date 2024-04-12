<?php

/**
 * Imageshop plugin for Craft CMS 4
 *
 * Integrate with an Imageshop account and use Imageshop resources in Craft
 *
 * @link      https://vangenplotz.no/
 * @copyright Copyright (c) 2018 Vangen & Plotz AS
 */

namespace trydig\craftimageshop;

// use trydig\craftimageshop\services\Image as ImageService;
// use trydig\craftimageshop\services\Soap as SoapService;
use trydig\craftimageshop\variables\ImageshopVariable;
use trydig\craftimageshop\models\Settings;
use trydig\craftimageshop\fields\ImageshopImage as ImageshopImageField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\utilities\ClearCaches;
use craft\i18n\PhpMessageSource;

use trydig\craftimageshop\services\Soap as SoapService;
use trydig\craftimageshop\services\Image as ImageService;
use trydig\craftimageshop\services\Cache as CacheService;

use yii\base\Event;
use yii\caching\TagDependency;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Vangen & Plotz AS
 * @package   Imageshop
 * @since     0.0.1
 *
 * @property  ImageshopService $imageshop
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftImageshop extends Plugin
{
    public const IMAGESHOP_CACHE_TAG = 'imageshop_objects';

    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftImageshop::$plugin
     *
     * @var Imageshop
     */
    public static CraftImageshop $plugin;


    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftImageshop::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Craft::$app->i18n->translations['craft-imageshop'] = [
        //     'class' => PhpMessageSource::class,
        //     'sourceLanguage' => 'en',
        //     'basePath' => __DIR__ . '/translations',
        //     'allowOverrides' => true,
        // ];

        $this->setComponents([
            'SoapService' => SoapService::class,
            'ImageService' => ImageService::class,
            'CacheService' => CacheService::class,
        ]);


        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'craftimageshop/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'craftimageshop/default/do-something';
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ImageshopImageField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftimageshop', ImageshopVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // Send them to our welcome screen
                    $request = Craft::$app->getRequest();
                    if ($request->isCpRequest) {
                        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl(
                            'settings/plugins/craft-imageshop'
                        ))->send();
                    }
                }
            }
        );

        // Adds a separate cache handling option for this plugin
        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            static function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'imageshop-cache',
                    'label' => Craft::t('craft-imageshop', 'Imageshop cache'),
                    'info' => Craft::t('craft-imageshop', 'This will clear all data related to Imageshop from the cache'),
                    'action' => [CraftImageshop::$plugin, 'invalidateCaches'],
                ];
            }
        );

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'craft-imageshop',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new Settings();
    }


    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {

        return Craft::$app->view->renderTemplate(
            'craft-imageshop/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * Invalidates all caches
     */
    public function invalidateCaches(): void
    {
        $cache = $this::getInstance()->CacheService->getCache();
        $cache->flush();

        Craft::info(
            Craft::t(
                'craft-imageshop',
                'Cache cleared',
            ),
            __METHOD__
        );
    }
}
