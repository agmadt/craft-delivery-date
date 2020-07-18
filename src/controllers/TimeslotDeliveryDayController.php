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

use Craft;
use craft\web\Controller;
use digitalbutter\craftdeliverydate\CraftDeliveryDate;
use digitalbutter\craftdeliverydate\models\Timeslot;

/**
 * TimeslotController Controller
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
class TimeslotDeliveryDayController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        return $this->renderTemplate('craft-delivery-date/_timeslot-delivery-days/index', [
            'timeslotDeliveryDays' => CraftDeliveryDate::getInstance()->getSettings()->getTimeslotDeliveryDays()
        ]);
    }

    public function actionStore()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();

        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $timeslotDeliveryDays = $settings->getTimeslotDeliveryDays();

        foreach ($params['deliverDay'] as $key => $deliverDay) {
            if ($deliverDay) {
                $timeslotDeliveryDays[$key]['enable'] = true;
            }
        }

        $settings->timeslotDeliveryDays = json_encode($timeslotDeliveryDays);

        if (!$settings->validate()) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return $this->redirectToPostedUrl();
        }

        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(CraftDeliveryDate::getInstance(), $settings->toArray());

        if (!$pluginSettingsSaved) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice('Settings saved.');
        return $this->redirectToPostedUrl();
    }

    public function actionCreateTimeslot()
    {
        $timeslotArr = [];
        $timeslots = CraftDeliveryDate::$plugin->timeslot->getAllTimeslots();

        foreach ($timeslots as $timeslot) {
            $timeslotArr[] = [
                'label' => $timeslot['name'],
                'value' => $timeslot['id'] . '-' . $timeslot['name'],
            ];
        }

        return $this->renderTemplate('craft-delivery-date/_timeslot-delivery-days/timeslots/create', [
            'timeslots' => $timeslotArr
        ]);
    }

    public function actionStoreTimeslot()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();
        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $timeslotDeliveryDays = $settings->getTimeslotDeliveryDays();

        $day = $params['day'];
        $timeslotID = explode('-', $params['time'])[0];
        $timeslotName = explode('-', $params['time'])[1];

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            if ($params['day'] == $key) {
                $timeslotDeliveryDay['timeslots'][$timeslotID] = [
                    'id' => $timeslotID,
                    'name' => $timeslotName
                ];
                $timeslotDeliveryDays[$key] = $timeslotDeliveryDay;
            }
        }

        $settings->timeslotDeliveryDays = json_encode($timeslotDeliveryDays);
        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(CraftDeliveryDate::getInstance(), $settings->toArray());

        if (!$pluginSettingsSaved) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return $this->redirect('delivery-date/settings/timeslot-delivery-days');
        }

        Craft::$app->getSession()->setNotice('Settings saved.');
        return $this->redirect('delivery-date/settings/timeslot-delivery-days');
    }

    public function actionDeleteTimeslot()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();
        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $timeslotDeliveryDays = $settings->getTimeslotDeliveryDays();

        $timeslotID = $params['id'];

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            if ($params['day'] == $key) {
                unset($timeslotDeliveryDay['timeslots'][$timeslotID]);
                $timeslotDeliveryDays[$key] = $timeslotDeliveryDay;
            }
        }

        $settings->timeslotDeliveryDays = json_encode($timeslotDeliveryDays);
        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(CraftDeliveryDate::getInstance(), $settings->toArray());

        if (!$pluginSettingsSaved) {
            Craft::$app->getSession()->setError('Couldn’t save settings.');
            return json_encode(['success' => false]);
        }

        return json_encode(['success' => true]);
    }
}
