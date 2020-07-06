<?php

use yii\db\Migration;

class m170629_194651_remove_column extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('gr_catalog_data', 'key');
        $this->createIndex('i_gr_catalog_data_value', 'gr_catalog_data', ['value'], false);
    }

    public function safeDown()
    {
        $this->addColumn('gr_catalog_data', 'key', $this->string());
        $this->dropIndex('i_gr_catalog_data_value', 'gr_catalog_data');

        echo "m170629_194651_remove_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170629_194651_remove_column cannot be reverted.\n";

        return false;
    }
    */
}
