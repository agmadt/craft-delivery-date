<?php

/**
 * craft-delivery-date plugin for Craft CMS 3.x
 *
 * Craft CMS Delivery Date Plugin
 *
 * @link      https://www.butter.com.hk
 * @copyright Copyright (c) 2020 Digital Butter
 */

namespace digitalbutter\craftdeliverydate\controllers;

use digitalbutter\craftdeliverydate\CraftDeliveryDate;

use Craft;
use craft\web\Controller;

/**
 * GeneralController Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Digital Butter
 * @package   CraftDeliveryDate
 * @since     1.0.0
 */
class GeneralController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Delivery Date Settings General Form
     */
    public function actionForm()
    {
        return $this->renderTemplate('craft-delivery-date/_general/form', [
            'settings' => CraftDeliveryDate::getInstance()->getSettings(),
            'cutOffTime' => CraftDeliveryDate::getInstance()->getSettings()->getCutOffTime()
        ]);
    }

    /**
     * Delivery Date Save General
     */
    public function actionStore()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();

        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $settings->minimumDaysAhead = $params['minimumDaysAhead'];
        $settings->maximumDaysAhead = $params['maximumDaysAhead'];
        $settings->cutOffTime = $params['cutOffTime'];

        if (!$settings->validate()) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return $this->renderTemplate('craft-delivery-date/general', ['settings' => $settings]);
        }

        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(CraftDeliveryDate::getInstance(), $settings->toArray());

        if (!$pluginSettingsSaved) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return $this->renderTemplate('craft-delivery-date/general', ['settings' => $settings]);
        }

        Craft::$app->getSession()->setNotice('Settings saved.');
        return $this->redirectToPostedUrl();
    }

    /**
     * Delivery Date Settings Block Days Form
     */
    public function actionBlockDays()
    {
        return $this->renderTemplate('craft-delivery-date/timeslots');
    }
}
