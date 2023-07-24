<?php

use yii\db\Migration;

/**
 * Class m230724_073113_init_rbac
 */
class m230724_073113_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $manageBook = $auth->createPermission('manageBook');
        $manageBook->description = 'Manage book CRUD';
        $auth->add($manageBook);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageBook);

        $userPermissions = $auth->createPermission('userPermissions');
        $auth->add($userPermissions);

        $user = $auth->createRole('user');
        $auth->add($user);
        $auth->addChild($user, $userPermissions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230724_073113_init_rbac cannot be reverted.\n";
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230724_073113_init_rbac cannot be reverted.\n";

        return false;
    }
    */
}
