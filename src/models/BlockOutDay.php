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

use craft\base\Model;

/**
 * CraftDeliveryDate Timeslot Model
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
class BlockOutDay extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * id model attribute
     *
     * @var integer
     */
    public $id;

    /**
     * name model attribute
     *
     * @var string
     */
    public $name;

    /**
     * start model attribute
     *
     * @var integer
     */
    public $start;

    /**
     * end model attribute
     *
     * @var integer
     */
    public $end;

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
            [['name', 'start'], 'required'],
        ];
    }
}
