<?php

class m140512_180435_create_issue_user_and_assignment_tables extends CDbMigration
{
	public function up()
	{
		$this->createTable('TBL_ISSUE', array(
		'ID' => 'pk',
		'NAME' => 'string NOT NULL',
		'DESCRIPTION' => 'text',
		'PROJECT_ID' => 'number DEFAULT NULL',
		'TYPE_ID' => 'number DEFAULT NULL',
		'STATUS_ID' => 'number DEFAULT NULL',
		'OWNER_ID' => 'number DEFAULT NULL',
		'REQUESTER_ID' => 'number DEFAULT NULL',
		'CREATE_TIME' => 'date DEFAULT NULL',
		'CREATE_USER_ID' => 'number DEFAULT NULL',
		'UPDATE_TIME' => 'date DEFAULT NULL',
		'UPDATE_USER_ID' => 'number DEFAULT NULL',
		));
		//create the user table
		$this->createTable('TBL_USER', array(
		'ID' => 'pk',
		'USERNAME' => 'string NOT NULL',
		'EMAIL' => 'string NOT NULL',
		'PASSWORD' => 'string NOT NULL',
		'LAST_LOGIN_TIME' => 'date DEFAULT NULL',
		'CREATE_TIME' => 'date DEFAULT NULL',
		'CREATE_USER_ID' => 'number DEFAULT NULL',
		'UPDATE_TIME' => 'date DEFAULT NULL',
		'UPDATE_USER_ID' => 'number DEFAULT NULL',
		));
		//create the assignment table that allows for many-to-many
		//relationship between projects and users
		$this->createTable('TBL_PROJECT_USER_ASSIGNMENT', array(
		'PROJECT_ID' => 'number NOT NULL',
		'USER_ID' => 'number NOT NULL',
		//'PRIMARY KEY (`PROJECT_ID`,`USER_ID`)',
		));
		//foreign key relationships
		//the TBL_ISSUE.project_ID is a reference to tbl_project.ID
		/*$this->addForeignKey("FK_ISSUE_PROJECT", "TBL_ISSUE", "PROJECT_ID", "TBL_PROJECT", "ID", "CASCADE", "RESTRICT");
		//the TBL_ISSUE.owner_ID is a reference to TBL_USER.ID
		$this->addForeignKey("FK_ISSUE_OWNER", "TBL_ISSUE", "OWNER_ID", "TBL_USER", "ID", "CASCADE", "RESTRICT");
		//the TBL_ISSUE.requester_ID is a reference to TBL_USER.ID
		$this->addForeignKey("FK_ISSUE_REQUESTER", "TBL_ISSUE", "REQUESTER_ID", "TBL_USER", "ID", "CASCADE", "RESTRICT");
		//the TBL_PROJECT_USER_ASSIGNMENT.project_ID is a reference to tbl_project.ID
		$this->addForeignKey("FK_PROJECT_USER", "TBL_PROJECT_USER_ASSIGNMENT", "PROJECT_ID", "TBL_PROJECT", "ID", "CASCADE","RESTRICT");
		//the TBL_PROJECT_USER_ASSIGNMENT.user_ID is a reference to tbl_user.ID
		$this->addForeignKey("FK_USER_PROJECT", "TBL_PROJECT_USER_ASSIGNMENT", "user_ID", "TBL_USER", "ID", "CASCADE", "RESTRICT");*/


	}

	public function down()
	{
		$this->truncateTable('TBL_PROJECT_USER_ASSIGNMENT');
		$this->truncateTable('TBL_ISSUE');
		$this->truncateTable('TBL_USER');
		$this->dropTable('TBL_PROJECT_USER_ASSIGNMENT');
		$this->dropTable('TBL_ISSUE');
		$this->dropTable('TBL_USER');
		
	}

	
	// Use safeUp/safeDown to do migration with transaction
	/*
	public function safeUp()
	{
		
	}

	public function safeDown()
	{
		
	}*/
	
}