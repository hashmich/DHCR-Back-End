Dear <?php echo $admin['AppUser']['first_name']; ?>,

a new course has been published!

In your role as a moderator, we would like to ask you to review the course as soon as possible.
Please check, if the course is related to DH and if the chosen tags are appropriate.
A guideline about which courses can be regarded as DH will be available soon.

# EXPECTED ACTIONS:
Hit "Approve" if you think this is a proper DH course.
In case you think there is something wrong, you may either edit the course data or unpublish the record.
However, you should contact the lecturer or the person that added the data.

The course has been added by:
<?php echo $course['AppUser']['name'].', '.$course['AppUser']['email']; ?>

The provided lecturer contact is:
<?php echo $course['Course']['contact_name'].', '.$course['Course']['contact_mail']; ?>


====================================================

# Quick Links:
Approve:    <?php echo Router::url('/courses/approve/'.$course['Course']['approval_token'], true); ?>

Login required:
Edit:       <?php echo Router::url('/courses/edit/'.$course['Course']['id'], true); ?>

Unpublish:  <?php echo Router::url('/courses/unpublish/'.$course['Course']['id'], true); ?>


====================================================

#The course details:
<?php
foreach($course['Course'] as $field => $value) {
    if(in_array($field, array('updated','user_id','active','approved',
        'approval_token','mod_mailed','last_reminder','course_parent_type_id',
        'skip_info_url','skip_guide_url','contact_mail','contact_name','lon','lat')))
        continue;
    // handle foreign keys
    if(strpos($field, '_id') !== false) {
        $model = Inflector::camelize(substr($field, 0, strlen($field) - 3));
        $value = $course[$model]['name'];
        $field = $model;
    }
    echo str_pad($field . ':', 24, " ") . "     ".$value."\n";
}
echo "\n#Disciplines:\n";
foreach($course['Discipline'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
echo "\n\n#Techniques\n";
foreach($course['TadirahTechnique'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
echo "\n\n#Objects\n";
foreach($course['TadirahObject'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
?>