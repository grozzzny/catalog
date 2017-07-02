<?php

use yii\db\Migration;

class m170702_104501_gr_catalog_categories_add_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('gr_catalog_categories', 'parents', $this->string());
    }

    public function safeDown()
    {

        $this->dropColumn('gr_catalog_categories', 'parents');
        echo "m170702_104501_gr_catalog_categories_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170702_104501_gr_catalog_categories_add_column cannot be reverted.\n";

        return false;
    }
    */
}
