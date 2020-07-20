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
use digitalbutter\craftdeliverydate\models\BlockOutDay as BlockOutDayModel;
use digitalbutter\craftdeliverydate\CraftDeliveryDate;

/**
 * BlockOutDay Service
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
class BlockOutDay extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftDeliveryDate::$plugin->blockOutDay->getAllBlockOutDays()
     *
     * @return mixed
     */
    public function getAllBlockOutDays()
    {
        return (new Query())
            ->select(['id', 'name', 'start', 'end'])
            ->from(['delivery_date_blocked_days'])
            ->all();
    }

    public function findBlockOutDayByID($id)
    {
        return (new Query())
            ->select(['id', 'name', 'start', 'end'])
            ->from(['delivery_date_blocked_days'])
            ->where(['id' => $id])
            ->one();
    }

    public function storeBlockOutDay($params)
    {
        $blockoutDay = new BlockOutDayModel();
        $blockoutDay->name = $params['name'];
        $blockoutDay->start = strtotime($params['start'] . ' 00:00:00');
        $blockoutDay->end = null;
        if ($params['type'] == 'multiple') {
            $blockoutDay->end = strtotime($params['end'] . ' 23:59:59');
        }

        if (!$blockoutDay->validate()) {
            Craft::$app->getSession()->setError('Some field are required.');
            return false;
        }

        Craft::$app->db->createCommand()
            ->insert('delivery_date_blocked_days', [
                'name' => $blockoutDay->name,
                'start' => $blockoutDay->start,
                'end' => $blockoutDay->end,
            ])
            ->execute();

        return true;
    }

    public function destroyBlockOutDay($params)
    {
        Craft::$app->db->createCommand()
            ->delete('delivery_date_blocked_days', ['id' => $params['id']])
            ->execute();

        return json_encode(['success' => true]);
    }
}
