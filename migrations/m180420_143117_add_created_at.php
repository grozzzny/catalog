<?php

use yii\db\Migration;

/**
 * Class m180420_143117_add_created_at
 */
class m180420_143117_add_created_at extends Migration
{

    public function up()
    {
        $this->addColumn('gr_catalog_categories', 'created_at', $this->integer());
        $this->addColumn('gr_catalog_categories', 'updated_at', $this->integer());
    }

    public function down()
    {
        echo "m180420_143117_add_created_at cannot be reverted.\n";

        $this->dropColumn('gr_catalog_categories', 'created_at');
        $this->dropColumn('gr_catalog_categories', 'updated_at');

        return false;
    }

}
