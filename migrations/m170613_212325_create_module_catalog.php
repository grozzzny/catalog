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
            'image' => $this->string(),
            'views' => $this->integer(),
            'short' => $this->string(),
            'description' => $this->text(),
            'status' => $this->boolean()->defaultValue(1),
            'order_num' => $this->integer()->defaultValue(100),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_items', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'category_id' => $this->integer(),
            'title' => $this->string(),
            'parent_id' => $this->integer(),
            'image' => $this->string(),
            'views' => $this->integer(),
            'short' => $this->string(),
            'description' => $this->text(),
            'status' => $this->boolean()->defaultValue(1),
            'order_num' => $this->integer()->defaultValue(100),
        ], $this->tableOptions);


        $this->createTable('gr_catalog_data', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(),
            'title' => $this->string(),
            'parent_id' => $this->integer(),
            'image' => $this->string(),
            'views' => $this->integer(),
            'short' => $this->string(),
            'description' => $this->text(),
            'status' => $this->boolean()->defaultValue(1),
            'order_num' => $this->integer()->defaultValue(100),
        ], $this->tableOptions);


        //'create_at' => $this->string(),
        //'datetime' => $this->integer(),
        //'datetime_update' => $this->integer(),
        //price

        $this->insert('easyii_modules', [
            'name' => 'callback',
            'class' => 'grozzzny\call_back\Module',
            'title' => 'Call back',
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
