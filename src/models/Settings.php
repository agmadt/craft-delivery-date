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
 * Craftdeliverydate Settings Model
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
    public $cutOffTime = '16:00';

    /**
     * timeSlot model attribute
     *
     * @var string
     */
    public $timeSlot = null;

    /**
     * blockOutDays model attribute
     *
     * @var string
     */
    public $blockOutDays = null;

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
}
