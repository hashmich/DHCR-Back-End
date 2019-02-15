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
        foreach(AppUser::$mapping as $k => $v) $_SERVER[$k] = $v;
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
        $result = $this->AppUser->shibLogin();
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

        $result = $this->AppUser->shibLogin();
        $this->assertNotEmpty($result);
        $this->assertEquals(1, count($result));

        #TODO: create more assertions to make sure the email mechanism works correctly
    }


    public function testIsShibUser() {
        $this->assertFalse($this->AppUser->isShibUser());
        $this->__setShibVars();
        $AppUser = new AppUser();
        $this->assertTrue($AppUser->isShibUser());
    }

}
?>