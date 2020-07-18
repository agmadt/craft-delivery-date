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

use craft\db\Query;

use craft;
use craft\base\Component;
use digitalbutter\craftdeliverydate\models\Timeslot as TimeslotModel;
use digitalbutter\craftdeliverydate\CraftDeliveryDate;

/**
 * Timeslot Service
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
class Timeslot extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftDeliveryDate::$plugin->timeslot->getAllTimeslots()
     *
     * @return mixed
     */
    public function getAllTimeslots()
    {
        return (new Query())
            ->select(['id', 'name', 'start', 'end'])
            ->from(['delivery_date_timeslots'])
            ->all();
    }

    public function findTimeslotByID($id)
    {
        return (new Query())
            ->select(['id', 'name', 'start', 'end'])
            ->from(['delivery_date_timeslots'])
            ->where(['id' => $id])
            ->one();
    }

    public function storeTimeslot($params)
    {
        $timeslot = new TimeslotModel();
        $timeslot->name = $params['name'];
        $timeslot->start = strtotime($params['start']['time']);
        $timeslot->end = strtotime($params['end']['time']);

        if (!$timeslot->validate()) {
            Craft::$app->getSession()->setError('Some field are required.');
            return false;
        }

        Craft::$app->db->createCommand()
            ->insert('delivery_date_timeslots', [
                'name' => $timeslot->name,
                'start' => $timeslot->start,
                'end' => $timeslot->end,
            ])
            ->execute();

        return true;
    }

    public function updateTimeslot($params)
    {
        $timeslot = new TimeslotModel();
        $timeslot->id = $params['id'];
        $timeslot->name = $params['name'];
        $timeslot->start = strtotime($params['start']['time']);
        $timeslot->end = strtotime($params['end']['time']);

        if (!$timeslot->validate()) {
            Craft::$app->getSession()->setError('Some field are required.');
            return false;
        }

        Craft::$app->db->createCommand()
            ->update('delivery_date_timeslots', [
                'name' => $timeslot->name,
                'start' => $timeslot->start,
                'end' => $timeslot->end,
            ], [
                'id' => $timeslot->id
            ])
            ->execute();

        return true;
    }

    public function destroyTimeslot($params)
    {
        Craft::$app->db->createCommand()
            ->delete('delivery_date_timeslots', ['id' => $params['id']])
            ->execute();

        return json_encode(['success' => true]);
    }
}
