Hello Admin, 

a new user has registered and is waiting for your approval of the new account!

If the field "Institution" is empty and instead the institution's name is
provided in the field "Other University", you will have to login and create a 
new institution entry.
If country and city of the new institution are unknown to the registry, 
you will also have to create these entries. 
Clicking on the link below will guide you through all required steps.

<?php
if(!empty($data[$model])) {
	echo "Details (database entry): \n\n";
	foreach($data[$model] as $fieldname => $value) {
        if(in_array($fieldname, array('modified','updated','password','shib_eppn','active','approved',
            'new_email','email_verified','is_admin','user_admin','last_login','password_token','email_token',
            'approval_token','password_token_expires','email_token_expires','approval_token_expires')))
            continue;
        // handle foreign keys
        if(strpos($fieldname, '_id') !== false) {
            $modelname = Inflector::classify(substr($fieldname, 0, strlen($fieldname) - 3));
            $value = $course[$modelname]['name'];
            $fieldname = $modelname;
        }
        switch($fieldname) {
			case 'university':
				echo str_pad('Other University:', 24, " ") . "\t\t" . $value . "\n";
				break;
			default:
				echo str_pad($fieldname . ':', 24, " ") . "\t\t" . $value . "\n";
		}
	}
	echo "\n\n";
	echo "Click here for instant approval: \n";

	echo Router::url(array(
		'admin' => false,
		'plugin' => 'users',
		'controller' => 'users',
		'action' => 'approve',
		$data[$model]['approval_token']
	), $full = true);
}
?>