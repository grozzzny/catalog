<?php


class m170613_212325_create_module_catalog extends \grozzzny\call_back\migrations\Migration
{
    public function safeUp()
    {
        $this->createTable('gr_catalog_categories', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'title' => $this->string(),
            'parent_id' => $this->integer(),
            'properties' => $this->text(),
            'image' => $this->string(),
            'views' => $this->integer(),
            'short' => $this->string(),
            'description' => $this->text(),
            'status' => $this->boolean()->defaultValue(1),
            'order_num' => $this->integer()->defaultValue(100),
        ], $this->tableOptions);

        $this->createTable('gr_catalog_rel', [
            'category_id' => $this->integer(),
            'item_id' => $this->integer(),
        ], $this->tableOptions);

        $this->createTable('gr_catalog_items', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'category_id' => $this->integer(),
            'image' => $this->string(),
            'title' => $this->string(),
            'short' => $this->string(),
            'description' => $this->text(),
            'price' => $this->integer(),
            'discount' => $this->integer(),
            'views' => $this->integer(),
            'time_create' => $this->integer(),
            'time_update' => $this->integer(),
            'create_at' => $this->integer(),
            'status' => $this->boolean()->defaultValue(false),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_data', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer(),
            'property_name' => $this->string(),
            'value' => 'varchar(1024)',
            'value_slug' => $this->string(),
        ], $this->tableOptions);


        $this->insert('easyii_modules', [
            'name' => 'Catalog',
            'class' => 'grozzzny\catalog\CatalogModule',
            'title' => 'New catalog',
            'icon' => 'font',
            'status' => 1,
            'settings' => '[]',
            'notice' => 0,
            'order_num' => 120
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('gr_call_back');
        $this->delete('easyii_modules',['name' => 'callback']);

        echo "m170613_212325_create_module_catalog cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170613_212325_create_module_catalog cannot be reverted.\n";

        return false;
    }
    */
}
