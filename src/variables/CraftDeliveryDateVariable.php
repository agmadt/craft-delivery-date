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
     *     {{ craft.craftdeliverydate.renderDatePicker }}
     *
     * @param null $optional
     * @return string
     */
    public function renderDatePicker()
    {
        $settings = CraftDeliveryDate::$plugin->getSettings();
        $timeslotDeliveryDays = json_decode($settings->timeslotDeliveryDays, true);
        $disabledDaysOfWeek = [];
        $blockedOutDays = CraftDeliveryDate::$plugin->blockOutDay->getAllBlockOutDays();
        $blockedOutDaysArr = [];

        foreach ($timeslotDeliveryDays as $key => $timeslotDeliveryDay) {
            if (!$timeslotDeliveryDay['enable']) {
                $disabledDaysOfWeek[] = array_search($key, $settings->deliveryDaysMap);
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

        $html = \Craft::$app->view->renderTemplate('craft-delivery-date/_frontend/datepicker', [
            'minimumDaysAhead' => $settings->getMinimumDaysAhead(),
            'maximumDaysAhead' => $settings->getMaximumDaysAhead(),
            'disabledDaysOfWeek' => implode(',', $disabledDaysOfWeek),
            'blockedOutDays' => $blockedOutDaysArr
        ]);

        \Craft::$app->view->setTemplateMode($oldMode);

        echo $html;
    }
}
