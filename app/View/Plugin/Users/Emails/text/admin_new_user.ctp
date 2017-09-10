Hello Admin, 

a new user has registered and is waiting for your approval of the new account!

If the field "institution_id" is empty and instead the institution's name is 
provided in the field "Other University", you will have to login and create a 
new institution entry.
If country and city of the new institution are unknown to the registry, 
you will also have to create these entries. 
Clicking on the link below will guide you through all required steps.  

<?php
if(!empty($data[$model])) {
	echo "Details (database entry): \n\n";
	foreach($data[$model] as $fieldname => $value) {
		switch($fieldname) {
			case 'university': 
				echo str_pad('Other University:', 24, " ") . "			" . $value . "\n";
				break;
			default:
				echo str_pad($fieldname . ':', 24, " ") . "			" . $value . "\n";
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
