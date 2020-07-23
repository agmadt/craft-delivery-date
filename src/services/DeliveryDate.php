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
}
