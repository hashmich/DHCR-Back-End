<?php
App::uses('AppUser', 'Model');

class AppUserTest extends CakeTestCase {
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->AppUser = ClassRegistry::init('AppUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->AppUser);

        parent::tearDown();
    }


    public function testConnectAccount() {
        $result = $this->AppUser->connectAccount();
        $this->assertEquals(array(), $result);
    }

}
?>