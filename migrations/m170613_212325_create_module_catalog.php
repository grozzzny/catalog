<?php


class m170613_212325_create_module_catalog extends \grozzzny\catalog\migrations\Migration
{
    public function safeUp()
    {
        $this->createTable('gr_catalog_categories', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'title' => $this->string(),
            'parent_id' => $this->integer()->notNull(),
            'image_file' => $this->string(),
            'views' => $this->integer()->notNull(),
            'short' => $this->string(),
            'description' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'status' => $this->boolean()->defaultValue(1),
            'order_num' => $this->integer()->notNull(),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_properties', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'title' => $this->string(),
            'type' => $this->string(),
            'settings' => $this->text(), // multiple | filter_range | filter_hidden
            'validation_rule' => $this->text(),
            'options' => $this->text(),
            'order_num' => $this->integer()->notNull(),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_items', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'image_file' => $this->string(),
            'title' => $this->string(),
            'short' => $this->string(),
            'description' => $this->text(),
            'price' => $this->integer(),
            'discount' => $this->integer(),
            'views' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'user_id' => $this->integer(),
            'status' => $this->boolean()->defaultValue(false),
            'order_num' => $this->integer()->notNull(),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_data', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer(),
            'property_slug' => $this->string(),
            'key' => $this->string(),
            'value' => 'varchar(1024)',
        ], $this->tableOptions);


        $this->createTable('gr_catalog_relations_categories_items', [
            'category_id' => $this->integer(),
            'item_id' => $this->integer(),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_relations_categories_properties', [
            'category_id' => $this->integer(),
            'property_id' => $this->integer(),
        ], $this->tableOptions);


        $this->createIndex('unique_catalog_relations_categories_items', 'gr_catalog_relations_categories_items', ['category_id','item_id'], true);
        $this->addForeignKey('fk_catalog_relations_categories_items_category_id', '{{%gr_catalog_relations_categories_items}}', 'category_id', '{{%gr_catalog_categories}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_catalog_relations_categories_items_item_id', '{{%gr_catalog_relations_categories_items}}', 'item_id', '{{%gr_catalog_items}}', 'id', 'CASCADE');


        $this->createIndex('unique_gr_catalog_categories_slug', 'gr_catalog_categories', ['slug'], true);
        $this->createIndex('unique_gr_catalog_items_slug', 'gr_catalog_items', ['slug'], true);


        $this->createIndex('unique_catalog_relations_categories_properties', 'gr_catalog_relations_categories_properties', ['category_id','property_id'], true);
        $this->addForeignKey('fk_catalog_relations_categories_properties_category_id', '{{%gr_catalog_relations_categories_properties}}', 'category_id', '{{%gr_catalog_categories}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_catalog_relations_categories_properties_property_id', '{{%gr_catalog_relations_categories_properties}}', 'property_id', '{{%gr_catalog_properties}}', 'id', 'CASCADE');

        $this->createIndex('unique_catalog_properties_slug', 'gr_catalog_properties', ['slug'], true);
        $this->createIndex('index_catalog_data_property_slug', 'gr_catalog_data', ['property_slug'], false);
        $this->createIndex('index_catalog_data_item_id', 'gr_catalog_data', ['item_id'], false);

        //Принцип ключа. Устанавливаем поведение на текущую колонку (Удаление или set null) на момент события указанного ИНДЕКСА в другой таблице
        $this->addForeignKey('fk_catalog_data_property_slug', '{{%gr_catalog_data}}', 'property_slug', '{{%gr_catalog_properties}}', 'slug', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_catalog_data_id', '{{%gr_catalog_data}}', 'item_id', '{{%gr_catalog_items}}', 'id', 'CASCADE');


        $this->insert('easyii_modules', [
            'name' => 'newcatalog',
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

        $this->dropForeignKey('fk_catalog_relations_categories_items_category_id','gr_catalog_relations_categories_items');
        $this->dropForeignKey('fk_catalog_relations_categories_items_item_id','gr_catalog_relations_categories_items');

        $this->dropForeignKey('fk_catalog_relations_categories_properties_category_id','gr_catalog_relations_categories_properties');
        $this->dropForeignKey('fk_catalog_relations_categories_properties_property_id','gr_catalog_relations_categories_properties');

        $this->dropForeignKey('fk_catalog_data_property_slug','gr_catalog_data');
        $this->dropForeignKey('fk_catalog_data_id','gr_catalog_data');

        $this->dropTable('gr_catalog_categories');
        $this->dropTable('gr_catalog_properties');
        $this->dropTable('gr_catalog_items');
        $this->dropTable('gr_catalog_relations_categories_items');
        $this->dropTable('gr_catalog_relations_categories_properties');
        $this->dropTable('gr_catalog_data');

        $this->delete('easyii_modules',['name' => 'newcatalog']);

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
