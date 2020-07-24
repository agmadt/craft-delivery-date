<?php

/**
 * craft-delivery-date plugin for Craft CMS 3.x
 *
 * Craft CMS Delivery Date Plugin
 *
 * @link      https://www.butter.com.hk
 * @copyright Copyright (c) 2020 Digital Butter
 */

namespace digitalbutter\craftdeliverydate\services;

use digitalbutter\craftdeliverydate\CraftDeliveryDate;

use craft\db\Query;
use Craft;
use craft\base\Component;

/**
 * DeliveryDate Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Digital Butter
 * @package   CraftDeliveryDate
 * @since     1.0.0
 */
class DeliveryDate extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftDeliveryDate::$plugin->deliveryDate->findDeliveryDateByColumn()
     *
     * @return mixed
     */
    public function findDeliveryDateByColumn($id, $value)
    {
        return (new Query())
            ->select(['id', 'order_id', 'delivery_date', 'timeslot'])
            ->from(['delivery_date_orders'])
            ->where([$id => $value])
            ->one();
    }

    public function registerDeliveryDateSession()
    {
        $params = Craft::$app->getRequest()->getBodyParams();
        // register plugin session if delivery date and timeslot have been chosen and exist in request upon cart updated
        if (
            isset($params['craft_delivery_date_datepicker'])
            && isset($params['craft_delivery_date_timeslot'])
        ) {
            Craft::$app->getSession()->set('craft_delivery_date_session', [
                'delivery_date' => $params['craft_delivery_date_datepicker'],
                'timeslot' => $params['craft_delivery_date_timeslot']
            ]);
        }
    }

    public function persistDeliveryDate()
    {
        // persist chosen delivery date and timeslot upon completed order to track and then delete the plugin session
        $pluginSession = Craft::$app->getSession()->get('craft_delivery_date_session');
        if ($pluginSession) {

            $timeslot = CraftDeliveryDate::$plugin->timeslot->findTimeslotByID($pluginSession['timeslot']);

            Craft::$app->db->createCommand()
                ->insert('delivery_date_orders', [
                    'order_id' => $e->sender->id,
                    'delivery_date' => \DateTime::createFromFormat('F j, Y', $pluginSession['delivery_date'])->getTimestamp(),
                    'timeslot' => json_encode([
                        'name' => $timeslot['name'],
                        'start' => $timeslot['start'],
                        'end' => $timeslot['end']
                    ]),
                ])
                ->execute();

            Craft::$app->getSession()->remove('craft_delivery_date_session');
        }
    }
}
