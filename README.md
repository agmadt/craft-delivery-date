
# craft-delivery-date plugin for Craft CMS 3.x

Craft CMS Delivery Date Plugin

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

`cd /path/to/project`

3. Then tell Composer to load the plugin:

`composer require /craft-delivery-date`

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for craft-delivery-date.

## craft-delivery-date Overview

Plugin designed to be used with Craft Commerce

## Configuring craft-delivery-date

This plugin is very much plug&play

**jQuery IS NOT included by default**

One thing to note that the plugin is not including jQuery by default, so if your theme does not include jQuery anywhere on the page, make sure to set `DELIVERY_DATE_INCLUDE_JQUERY=true` in `.env` config file.

## Using craft-delivery-date

**Variables**

`craft.craftdeliverydate.render` -- will render a datepicker, select option, and submit button along with ajax, make sure to add some javascript validation before user able to press submit checkout button.

`craft.craftdeliverydate.renderChosenDeliveryDate(orderID)` -- will render a delivery date for a specific orders

`craft.craftdeliverydate.renderChosenTimeslot(orderID)` -- will render a timeslot for a specific orders.


## craft-delivery-date Roadmap

Some things to do, and ideas for potential features: [Issues labeled with enhancement](https://github.com/agmadt/craft-delivery-date/issues?q=is%3Aopen+is%3Aissue+label%3Aenhancement)

Brought to you by [Digital Butter](https://www.butter.com.hk)