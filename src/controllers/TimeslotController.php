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
class TimeslotController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        return $this->renderTemplate('craft-delivery-date/_timeslots/index', [
            'timeslots' => CraftDeliveryDate::$plugin->timeslot->getAllTimeslots()
        ]);
    }

    public function actionCreate()
    {
        return $this->renderTemplate('craft-delivery-date/_timeslots/create', [
            'timeslot' => new Timeslot()
        ]);
    }

    public function actionStore()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();

        $savedTimeslot = CraftDeliveryDate::$plugin->timeslot->storeTimeslot($params);

        if (!$savedTimeslot) {
            Craft::$app->getSession()->setError('Couldn’t create timeslot.');
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice('Timeslot created.');
        return $this->redirectToPostedUrl();
    }

    public function actionEdit(int $timeslotID)
    {
        $editedTimeslot = CraftDeliveryDate::$plugin->timeslot->findTimeslotByID($timeslotID);

        if (!$editedTimeslot) {
            Craft::$app->getSession()->setError('Timeslot not found.');
            return $this->redirect('delivery-date/settings/timeslots');
        }

        $timeslot = new Timeslot();
        $timeslot->id = $editedTimeslot['id'];
        $timeslot->name = $editedTimeslot['name'];
        $timeslot->start = date('g:i A', $editedTimeslot['start']);
        $timeslot->end = date('g:i A', $editedTimeslot['end']);

        return $this->renderTemplate('craft-delivery-date/_timeslots/edit', [
            'timeslot' => $timeslot
        ]);
    }

    public function actionUpdate(int $timeslotID)
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();
        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $timeslotDeliveryDays = $settings->getTimeslotDeliveryDays();

        $params['id'] = $timeslotID;
        $updatedTimeslot = CraftDeliveryDate::$plugin->timeslot->updateTimeslot($params);
        print_r($params['start']['time']);

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            foreach ($timeslotDeliveryDay['timeslots'] as $k => $timeslot) {
                if ($params['id'] == $k) {
                    $timeslot['name'] = $params['name'];
                    $timeslot['start'] = strtotime($params['start']['time']);
                    $timeslot['end'] = strtotime($params['end']['time']);
                    $timeslotDeliveryDays[$key]['timeslots'][$k] = $timeslot;
                }
            }
        }

        $settings->timeslotDeliveryDays = json_encode($timeslotDeliveryDays);
        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(CraftDeliveryDate::getInstance(), $settings->toArray());

        if (!$updatedTimeslot) {
            Craft::$app->getSession()->setError('Couldn’t update timeslot.');
            return $this->redirect('delivery-date/settings/timeslots');
        }

        Craft::$app->getSession()->setNotice('Timeslot updated.');
        return $this->redirect('delivery-date/settings/timeslots');
    }

    public function actionDestroy()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();
        $settings = CraftDeliveryDate::getInstance()->getSettings();
        $timeslotDeliveryDays = $settings->getTimeslotDeliveryDays();

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            foreach ($timeslotDeliveryDay['timeslots'] as $k => $timeslot) {
                if ($params['id'] == $k) {
                    return json_encode(['success' => false]);
                }
            }
        }

        $updatedTimeslot = CraftDeliveryDate::$plugin->timeslot->destroyTimeslot($params);
        return json_encode(['success' => true]);
    }
}
