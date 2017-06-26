<?php

use yii\db\Migration;

class m170626_093456_add_column_properties_index extends Migration
{
    public function safeUp()
    {
        $this->addColumn('gr_catalog_properties','index',$this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('gr_catalog_properties','index');
        echo "m170626_093456_add_column_properties_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170626_093456_add_column_properties_index cannot be reverted.\n";

        return false;
    }
    */
}
