<?php

namespace xutl\mq\migrations;

use yii\db\Migration;

class M170221055130Create_message_queue_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%message_queue}}', [
            'id' => $this->primaryKey(),
            'queue' => $this->string()->notNull()->comment('队列名称'),
            'payload' => $this->text()->notNull()->comment('载荷'),
            'available_at' => $this->integer()->unsigned()->comment('可以获取的时间'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%message_queue}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
