<?php
App::uses('MultitermsController', 'Multiterm.Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');


class MultitermsControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'plugin.multiterm.node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		ob_flush();
		parent::tearDown();
	}

/**
 * testNodes
 *
 * @return void
 */
	public function testMissingTermInUrl() {

		$this->testAction('/multiterm/multiterms/view/');
		$this->assertEquals($this->headers['Location'], 'http://'. $_SERVER['HTTP_HOST'] . $this->controller->webroot);
	}
	
	public function testOneTerm() {
		
		$this->testAction('/multiterm/multiterms/view/slugs:uncategorized');
		$this->assertEqual(count($this->vars['nodes']), 1);		
	}

	public function testOneTermByType() {
		
		$this->testAction('/multiterm/multiterms/view/type:page/slugs:uncategorized');
		$this->assertEmpty($this->vars['nodes']);	
	}	

	public function testMultiTerm() {
		
		$this->testAction('/multiterm/multiterms/view/slugs:uncategorized,announcements');
		$this->assertEqual(count($this->vars['nodes']), 2);		
	}	

	public function testMultiTermEmptyResult() {
		
		$this->testAction('/multiterm/multiterms/view/slugs:ncategorized');
		$this->assertEmpty($this->vars['nodes']);
	}	


}
