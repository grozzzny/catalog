<?php

use yii\db\Migration;

/**
 * Class m181210_085442_add_column_category_slug
 */
class m181210_085442_add_column_category_slug extends Migration
{
    public function up()
    {
        $this->addColumn('{{%gr_catalog_items}}', 'category_slug', $this->string());
        $this->addForeignKey('fk_catalog_items_slug', '{{%gr_catalog_items}}', 'category_slug', '{{%gr_catalog_categories}}', 'slug', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_catalog_items_slug', '{{%gr_catalog_items}}');
        $this->dropColumn('{{%gr_catalog_items}}', 'category_slug');
        
        echo "m181210_085442_add_column_category_slug cannot be reverted.\n";

        return false;
    }
}
