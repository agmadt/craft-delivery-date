<?php

/**
 * craft-delivery-date plugin for Craft CMS 3.x
 *
 * Craft CMS Delivery Date Plugin
 *
 * @link      https://www.butter.com.hk
 * @copyright Copyright (c) 2020 Digital Butter
 */

namespace digitalbutter\craftdeliverydate;

use digitalbutter\craftdeliverydate\services\DeliveryDate as DeliveryDateService;
use digitalbutter\craftdeliverydate\variables\CraftDeliveryDateVariable;
use digitalbutter\craftdeliverydate\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Digital Butter
 * @package   CraftDeliveryDate
 * @since     1.0.0
 *
 * @property  DeliveryDateService $deliveryDate
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftDeliveryDate extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Craftdeliverydate::$plugin
     *
     * @var CraftDeliveryDate
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Craftdeliverydate::$plugin
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

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['delivery-date'] = 'craft-delivery-date/delivery-date/redirect';
                $event->rules['delivery-date/settings'] = 'craft-delivery-date/delivery-date/redirect';

                $event->rules['delivery-date/settings/general'] = 'craft-delivery-date/general/form';
                $event->rules['delivery-date/settings/general/store'] = 'craft-delivery-date/general/store';

                $event->rules['delivery-date/settings/timeslot-delivery-days'] = 'craft-delivery-date/timeslot-delivery-day/index';
                $event->rules['delivery-date/settings/timeslot-delivery-days/store'] = 'craft-delivery-date/timeslot-delivery-day/store';
                $event->rules['delivery-date/settings/timeslot-delivery-days/timeslots/create'] = 'craft-delivery-date/timeslot-delivery-day/create-timeslot';
                $event->rules['delivery-date/settings/timeslot-delivery-days/timeslots/store'] = 'craft-delivery-date/timeslot-delivery-day/store-timeslot';
                $event->rules['delivery-date/settings/timeslot-delivery-days/timeslots/delete'] = 'craft-delivery-date/timeslot-delivery-day/delete-timeslot';

                $event->rules['delivery-date/settings/blockoutdays'] = 'craft-delivery-date/block-out-day/index';
                $event->rules['delivery-date/settings/save-blockoutdays'] = 'craft-delivery-date/delivery-date/save-blockoutdays';

                $event->rules['delivery-date/settings/timeslots'] = 'craft-delivery-date/timeslot/index';
                $event->rules['delivery-date/settings/timeslots/create'] = 'craft-delivery-date/timeslot/create';
                $event->rules['delivery-date/settings/timeslots/store'] = 'craft-delivery-date/timeslot/store';
                $event->rules['delivery-date/settings/timeslots/edit/<timeslotID:\d+>'] = 'craft-delivery-date/timeslot/edit';
                $event->rules['delivery-date/settings/timeslots/update/<timeslotID:\d+>'] = 'craft-delivery-date/timeslot/update';
                $event->rules['delivery-date/settings/timeslots/delete'] = 'craft-delivery-date/timeslot/destroy';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftdeliverydate', CraftdeliverydateVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        // Register the services
        $this->setComponents([
            'timeslot' => \digitalbutter\craftdeliverydate\services\Timeslot::class,
        ]);

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
                'craft-delivery-date',
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
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('delivery-date/settings/general'));
    }
}
