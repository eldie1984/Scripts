<?php

class m140512_175743_create_project_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('TBL_PROJECT', array(
			'ID' => 'pk',
			'NAME' => 'string NOT NULL',
			'DESCRIPTION' => 'string NOT NULL',
			'CREATE_TIME' => 'date DEFAULT NULL',
			'CREATE_USER_ID' => 'number DEFAULT NULL',
			'UPDATE_TIME' => 'date DEFAULT NULL',
			'UPDATE_USER_ID' => 'number DEFAULT NULL',
			));
	}

	public function down()
	{
		$this->dropTable('TBL_PROJECT');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}