<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%yiipurchases}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%users}}`
 * - `{{%books}}`
 * - `{{%yiicoupons}}`
 */
class m230804_071418_create_yiipurchases_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%yiipurchases}}', [
            'id' => $this->primaryKey(),
            'User_Id' => $this->integer()->notNull(),
            'Book_Id' => $this->integer()->notNull(),
            'Coupon_Id' => $this->integer(),
            'Quantity' => $this->integer()->notNull(),
            'Unit_Price' => $this->integer()->notNull(),
            'Total_Price' => $this->integer()->notNull(),
            'Discount' => $this->integer()->notNull(),
            'Amount_Paid' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `User_Id`
        $this->createIndex(
            '{{%idx-yiipurchases-User_Id}}',
            '{{%yiipurchases}}',
            'User_Id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-yiipurchases-User_Id}}',
            '{{%yiipurchases}}',
            'User_Id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        // creates index for column `Book_Id`
        $this->createIndex(
            '{{%idx-yiipurchases-Book_Id}}',
            '{{%yiipurchases}}',
            'Book_Id'
        );

        // add foreign key for table `{{%books}}`
        $this->addForeignKey(
            '{{%fk-yiipurchases-Book_Id}}',
            '{{%yiipurchases}}',
            'Book_Id',
            '{{%books}}',
            'id',
            'CASCADE'
        );

        // creates index for column `Coupon_Id`
        $this->createIndex(
            '{{%idx-yiipurchases-Coupon_Id}}',
            '{{%yiipurchases}}',
            'Coupon_Id'
        );

        // add foreign key for table `{{%yiicoupons}}`
        $this->addForeignKey(
            '{{%fk-yiipurchases-Coupon_Id}}',
            '{{%yiipurchases}}',
            'Coupon_Id',
            '{{%yiicoupons}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-yiipurchases-User_Id}}',
            '{{%yiipurchases}}'
        );

        // drops index for column `User_Id`
        $this->dropIndex(
            '{{%idx-yiipurchases-User_Id}}',
            '{{%yiipurchases}}'
        );

        // drops foreign key for table `{{%books}}`
        $this->dropForeignKey(
            '{{%fk-yiipurchases-Book_Id}}',
            '{{%yiipurchases}}'
        );

        // drops index for column `Book_Id`
        $this->dropIndex(
            '{{%idx-yiipurchases-Book_Id}}',
            '{{%yiipurchases}}'
        );

        // drops foreign key for table `{{%yiicoupons}}`
        $this->dropForeignKey(
            '{{%fk-yiipurchases-Coupon_Id}}',
            '{{%yiipurchases}}'
        );

        // drops index for column `Coupon_Id`
        $this->dropIndex(
            '{{%idx-yiipurchases-Coupon_Id}}',
            '{{%yiipurchases}}'
        );

        $this->dropTable('{{%yiipurchases}}');
    }
}
