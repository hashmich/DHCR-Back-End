<?php
App::uses('AppUser', 'Model');

class AppUserTest extends CakeTestCase {

    public $fixtures = array('app.app_user');

    public function setUp() {
        parent::setUp();
        $this->AppUser = ClassRegistry::init('AppUser');
        $this->AppUser->belongsTo = array();
    }


    public function tearDown() {
        unset($this->AppUser);

        parent::tearDown();
    }

    private function __setShibVars() {
        foreach(AppUser::$shib_mapping as $k => $v) $_SERVER[$k] = $v;
    }


    public function testConnectAccount() {
        $result = $this->AppUser->connectAccount();
        $this->assertEquals(array(), $result);

        $this->tearDown();
        $this->__setShibVars();
        $this->setUp();

        $this->AppUser->id = 1;
        $authUser = array('id' => 2, 'shib_eppn' => 'old_eppn');
        $result = $this->AppUser->connectAccount($authUser);
        $this->assertNotEmpty($result);

        $this->assertEquals('shib_eppn', $result['shib_eppn']);
        $this->assertEquals(2, $result['id']);
    }


    public function testShibLogin() {
		$user = array();
    	$result = $this->AppUser->shibLogin($user);
        $this->assertFalse($result);

        $this->tearDown();
        $this->__setShibVars();
        $this->setUp();
        // creating an user with just that shib_eppn...
        $this->AppUser->id = 2;
        $this->AppUser->save($this->AppUser->data, false);
        // set the data...
        $this->AppUser->read();
        $this->assertTrue($this->AppUser->isShibUser());
        
        $result = $this->AppUser->shibLogin($user);
		$this->assertEquals(1, count($user));
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($user);
		
        // assert that we don't get identification on double entries!
		$this->AppUser->data['id'] = 3;
		$this->AppUser->data['shib_eppn'] = 'other';
		$this->AppUser->data['email'] = 'other';
		$this->AppUser->save($this->AppUser->data, false);
		$this->AppUser->read();
	
		$this->AppUser->data['AppUser']['shib_eppn'] = 'foo';
		$this->AppUser->data['AppUser']['email'] = 'bar';
		
		// test to retrieve ambiguous users for further processing
		$result = $this->AppUser->shibLogin($user);
		$this->assertFalse($result);
		$this->assertEquals(2, count($user));
    }


    public function testIsShibUser() {
        $this->assertFalse($this->AppUser->isShibUser());
        $this->__setShibVars();
        $AppUser = new AppUser();
        $this->assertTrue($AppUser->isShibUser());
    }
    
    
    public function testGetModerators() {
    	// get admins only
    	$result = $this->AppUser->getModerators($country_id = null, $user_admin = false);
    	$this->assertNotEmpty($result);
    	foreach($result as $user) {
    		$this->assertTrue($user['AppUser']['is_admin'] || ($user['AppUser']['user_role_id'] == 1));
		}
    	// get user admins only
		$result = $this->AppUser->getModerators($country_id = null, $user_admin = true);
		$this->assertNotEmpty($result);
		foreach($result as $user) {
			$this->assertTrue($user['AppUser']['user_admin']);
		}
		// get national moderators
		$result = $this->AppUser->getModerators($country_id = 1, $user_admin = false);
		$this->assertNotEmpty($result);
	}

}
?>