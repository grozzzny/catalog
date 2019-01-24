<?php

use yii\db\Migration;

/**
 * Class m190123_093827_ref_column_catalog
 */
class m190123_093827_ref_column_catalog extends Migration
{

    public function up()
    {
        $this->createIndex('i_gr_catalog_categories_parent_id', 'gr_catalog_categories', 'parent_id');
        $this->createIndex('i_gr_catalog_categories_views', 'gr_catalog_categories', 'views');
        $this->createIndex('i_gr_catalog_categories_updated_at', 'gr_catalog_categories', 'updated_at');
        $this->createIndex('i_gr_catalog_categories_status', 'gr_catalog_categories', 'status');
        $this->createIndex('i_gr_catalog_categories_order_num', 'gr_catalog_categories', 'order_num');
        $this->createIndex('i_gr_catalog_categories_parents', 'gr_catalog_categories', 'parents');

        $this->createIndex('i_gr_catalog_items_views', 'gr_catalog_items', 'views');
        $this->createIndex('i_gr_catalog_items_updated_at', 'gr_catalog_items', 'updated_at');
        $this->createIndex('i_gr_catalog_items_status', 'gr_catalog_items', 'status');
        $this->createIndex('i_gr_catalog_items_order_num', 'gr_catalog_items', 'order_num');

        $this->alterColumn('gr_catalog_items', 'views', $this->integer()->defaultValue(0));
        $this->alterColumn('gr_catalog_items', 'order_num', $this->integer()->defaultValue(0));

    }

    public function down()
    {
        echo "m190123_093827_ref_column_catalog cannot be reverted.\n";

        return false;
    }
}
