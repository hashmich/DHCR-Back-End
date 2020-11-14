<?php

App::uses('AppModel', 'Model');
class Notification extends AppModel {
    /**
     * Notifications are a list of notifications already
     * sent to subscribers for a specific course.
     * Use as a negative list to not mail a subscriber again
     * about the same course (eg. after tag update).
     */
    public $displayField = 'subscription_id';

}
