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
        return $this->renderTemplate('craft-delivery-date/_block-out-days/index', [
            'timeslots' => CraftDeliveryDate::$plugin->timeslot->getAllTimeslots()
        ]);
    }
}
