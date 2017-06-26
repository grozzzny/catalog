<?php

use yii\db\Migration;

class m170626_072216_rename_validation_rule extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('gr_catalog_properties', 'validation_rule', 'validations');
    }

    public function safeDown()
    {
        $this->renameColumn('gr_catalog_properties', 'validations', 'validation_rule');

        echo "m170626_072216_rename_validation_rule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170626_072216_rename_validation_rule cannot be reverted.\n";

        return false;
    }
    */
}
