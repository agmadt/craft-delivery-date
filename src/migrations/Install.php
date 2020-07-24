<?php

namespace digitalbutter\craftdeliverydate\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('delivery_date_timeslots', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'start' => $this->integer(),
            'end' => $this->integer(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('delivery_date_blocked_days', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'start' => $this->integer(),
            'end' => $this->integer(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('delivery_date_orders', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'delivery_date' => $this->integer()->notNull(),
            'timeslot' => $this->string()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('delivery_date_timeslots');
        $this->dropTableIfExists('delivery_date_blocked_days');
        $this->dropTableIfExists('delivery_date_orders');
    }
}
