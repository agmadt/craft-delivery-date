<?php

/**
 * craft-delivery-date plugin for Craft CMS 3.x
 *
 * Craft CMS Delivery Date Plugin
 *
 * @link      https://www.butter.com.hk
 * @copyright Copyright (c) 2020 Digital Butter
 */

namespace digitalbutter\craftdeliverydate\models;

use digitalbutter\craftdeliverydate\CraftDeliveryDate;

use Craft;
use craft\base\Model;

/**
 * CraftDeliveryDate Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Digital Butter
 * @package   CraftDeliveryDate
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * minimumDaysAhead model attribute
     *
     * @var integer
     */
    public $minimumDaysAhead = 2;

    /**
     * maximumDaysAhead model attribute
     *
     * @var integer
     */
    public $maximumDaysAhead = 30;

    /**
     * cutOffTime model attribute
     *
     * @var string
     */
    public $cutOffTime = '4:00 PM';

    /**
     * availableTimeslots model attribute
     *
     * @var string
     */
    public $availableTimeslots = null;

    /**
     * timeslots model attribute
     *
     * @var string
     */
    public $timeslotDeliveryDays;

    /**
     * blockOutDays model attribute
     *
     * @var string
     */
    public $blockOutDays;

    public $deliveryDaysMap = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
    ];

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['minimumDaysAhead', 'maximumDaysAhead', 'cutOffTime'], 'required'],
        ];
    }

    public function getMinimumDaysAhead()
    {
        $today = new \DateTime();
        return $today->modify('+' . $this->minimumDaysAhead . ' day')->format('F j, Y');
    }

    public function getMaximumDaysAhead()
    {
        $today = new \DateTime();
        return $today->modify('+' . $this->maximumDaysAhead . ' day')->format('F j, Y');
    }

    public function getCutOffTime()
    {
        if (!isset($this->cutOffTime['time'])) {
            return $this->cutOffTime;
        }

        return $this->cutOffTime['time'];
    }

    public function getTimeslotDeliveryDays()
    {
        if (!$this->timeslotDeliveryDays) {
            return [
                'sunday' => ['title' => 'Sunday', 'enable' => false, 'timeslots' => []],
                'monday' => ['title' => 'Monday', 'enable' => false, 'timeslots' => []],
                'tuesday' => ['title' => 'Tuesday', 'enable' => false, 'timeslots' => []],
                'wednesday' => ['title' => 'Wednesday', 'enable' => false, 'timeslots' => []],
                'thursday' => ['title' => 'Thursday', 'enable' => false, 'timeslots' => []],
                'friday' => ['title' => 'Friday', 'enable' => false, 'timeslots' => []],
                'saturday' => ['title' => 'Saturday', 'enable' => false, 'timeslots' => []],
            ];
        }

        return json_decode($this->timeslotDeliveryDays, true);
    }
}
