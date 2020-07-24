<?php

/**
 * craft-delivery-date plugin for Craft CMS 3.x
 *
 * Craft CMS Delivery Date Plugin
 *
 * @link      https://www.butter.com.hk
 * @copyright Copyright (c) 2020 Digital Butter
 */

namespace digitalbutter\craftdeliverydate\variables;

use digitalbutter\craftdeliverydate\CraftDeliveryDate;

use Craft;
use craft\web\View;

/**
 * craft-delivery-date Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftdeliverydate }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Digital Butter
 * @package   CraftDeliveryDate
 * @since     1.0.0
 */
class CraftDeliveryDateVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.craftdeliverydate.render }}
     *
     * @param null $optional
     * @return string
     */
    public function render()
    {
        $settings = CraftDeliveryDate::$plugin->getSettings();
        $timeslotDeliveryDays = json_decode($settings->timeslotDeliveryDays, true);
        $blockedOutDays = CraftDeliveryDate::$plugin->blockOutDay->getAllBlockOutDays();
        $disabledDaysOfWeek = [];
        $blockedOutDaysArr = [];
        $daysTimeslots = [];
        $choosenDeliveryDate = null;
        $choosenDeliveryWeekName = null;
        $choosenTimeslot = null;
        $pluginSession = Craft::$app->getSession()->get('craft_delivery_date_session');

        if ($pluginSession && $pluginSession['delivery_date'] && $pluginSession['timeslot']) {
            $choosenDeliveryDate = $pluginSession['delivery_date'];
            $choosenDeliveryWeekName = strtolower(\DateTime::createFromFormat('F j, Y', $choosenDeliveryDate)->format('l'));
            $choosenTimeslot = $pluginSession['timeslot'];
        }

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            if (!$timeslotDeliveryDay['enable']) {
                $disabledDaysOfWeek[] = array_search($key, $settings->deliveryDaysMap);
            }

            foreach ($timeslotDeliveryDay['timeslots'] as $timeslot) {
                $daysTimeslots[$key][] = [
                    'id' => $timeslot['id'],
                    'name' => $timeslot['name'],
                    'start' => date('g:i A', $timeslot['start']),
                    'end' => date('g:i A', $timeslot['end']),
                ];
            }
        }

        foreach ($blockedOutDays as $key => $blockedOutDay) {
            // If end is empty, that means it only block out one day
            if (empty($blockedOutDay['end'])) {
                $blockedOutDaysArr[] = $blockedOutDay['start'];
                continue;
            }

            $daterange = new \DatePeriod(
                new \DateTime(date('Y-m-d', $blockedOutDay['start'])),
                new \DateInterval('P1D'),
                (new \DateTime(date('Y-m-d', $blockedOutDay['end'])))->modify('+1 day')
            );

            foreach ($daterange as $date) {
                $blockedOutDaysArr[] = $date->format('F j, Y');
            }
        }

        $oldMode = \Craft::$app->view->getTemplateMode();
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = \Craft::$app->view->renderTemplate('craft-delivery-date/_frontend/default', [
            'minimumDaysAhead' => $settings->getMinimumDaysAhead(),
            'maximumDaysAhead' => $settings->getMaximumDaysAhead(),
            'disabledDaysOfWeek' => implode(',', $disabledDaysOfWeek),
            'blockedOutDays' => $blockedOutDaysArr,
            'daysTimeslots' => $daysTimeslots,
            'choosen_delivery_date' => $choosenDeliveryDate,
            'choosen_delivery_week_name' => $choosenDeliveryWeekName,
            'choosen_timeslot' => $choosenTimeslot,
        ]);

        \Craft::$app->view->setTemplateMode($oldMode);

        echo $html;
    }

    public function renderChosenDeliveryDate($orderID)
    {
        $deliveryDate = CraftDeliveryDate::$plugin->deliveryDate->findDeliveryDateByColumn('order_id', $orderID);

        if (!$deliveryDate) {
            return '';
        }

        $date = date('F j, Y', $deliveryDate['delivery_date']);

        return $date;
    }

    public function renderChosenTimeslot($orderID)
    {
        $deliveryDate = CraftDeliveryDate::$plugin->deliveryDate->findDeliveryDateByColumn('order_id', $orderID);

        if (!$deliveryDate) {
            return '';
        }

        $timeslot = json_decode($deliveryDate['timeslot'], true);

        return $timeslot['name'];
    }
}
