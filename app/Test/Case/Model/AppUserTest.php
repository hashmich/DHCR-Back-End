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


    public function testConnectAccount() {
        $result = $this->AppUser->connectAccount();
        $this->assertEquals(array(), $result);

        // test with server shib variables being set
        foreach(AppUser::$mapping as $k => $v) {
            //$_SERVER[$k] = $v;
        }
        //$AppUser = new AppUser();
        $this->AppUser->id = 1;
        $result = $this->AppUser->connectAccount();
        $this->assertNotEmpty($result);
    }


    public function testIsShibUser() {
        $this->assertFalse($this->AppUser->isShibUser());
        // test with server shib variables being set
        foreach(AppUser::$mapping as $k => $v) {
            $_SERVER[$k] = $v;
        }
        $AppUser = new AppUser();
        $this->assertTrue($AppUser->isShibUser());
    }

}
?>