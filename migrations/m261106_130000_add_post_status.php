<?php

use yii\db\Migration;

class m261106_130000_add_post_status extends Migration
{
    public function up()
    {
        $this->addColumn('{{%post}}', 'status', $this->string(20)->notNull()->defaultValue('draft'));
        $this->addColumn('{{%post}}', 'rejection_reason', $this->text()->null());

        // Artikel yang sudah ada sebelumnya tetap tampil di beranda.
        $this->update('{{%post}}', ['status' => 'published']);
    }

    public function down()
    {
        $this->dropColumn('{{%post}}', 'rejection_reason');
        $this->dropColumn('{{%post}}', 'status');
    }
}