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
class BlockOutDayController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        $arr = [];
        $blockOutDays = CraftDeliveryDate::$plugin->blockOutDay->getAllBlockOutDays();

        foreach ($blockOutDays as $day) {
            // end date is added by 1
            // https://fullcalendar.io/docs/Calendar-select
            // https://stackoverflow.com/a/27407217/2971496
            $arr[] = [
                'id' => $day['id'],
                'title' => $day['name'],
                'start' => date('Y-m-d', $day['start']),
                'end' => !empty($day['end']) ? date('Y-m-d', strtotime('+1 day', $day['end'])) : null
            ];
        }

        return $this->renderTemplate('craft-delivery-date/_block-out-days/index', [
            'blockOutDays' => json_encode($arr)
        ]);
    }

    public function actionStore()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();

        $savedBlockOutDay = CraftDeliveryDate::$plugin->blockOutDay->storeBlockOutDay($params);

        if (!$savedBlockOutDay) {
            Craft::$app->getSession()->setError('Couldn’t create block out day.');
            throw new \yii\web\NotFoundHttpException();
        }
    }

    public function actionDestroy()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();

        $deletedBlockOutDay = CraftDeliveryDate::$plugin->blockOutDay->destroyBlockOutDay($params);

        if (!$deletedBlockOutDay) {
            Craft::$app->getSession()->setError('Couldn’t delete block out day.');
            throw new \yii\web\NotFoundHttpException();
        }
    }
}
