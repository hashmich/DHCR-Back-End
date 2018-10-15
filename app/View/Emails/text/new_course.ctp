Dear Moderator,

a new course has been published!

In your role as a moderator, we would like to ask you to review the course as soon as possible.
Please check, if the course is related to DH and if the chosen tags are appropriate.
A guideline about which courses can be regarded as DH will be available soon.

# EXPECTED ACTIONS:
You may either edit the course data or unpublish the record in case there is something wrong.
However, you should contact the person that added the data or the lecturer.

The course has been added by:
<?php echo $this->Html->link($course['AppUser']['name'], 'mailto:'.$course['AppUser']['email']); ?>
The provided lecturer contact is:
<?php echo $this->Html->link($course['Course']['contact_name'], 'mailto:'.$course['Course']['contact_mail']); ?>

--

# Quick Links:
<?php echo $this->Html->link('Approve', '/courses/approve/'.$couse['approval_token']); ?> |
<?php echo $this->Html->link('Edit', '/courses/edit/'.$course['id']); ?> |
<?php echo $this->Html->link('Unpublish', '/courses/unpublish/'.$course['id']); ?>

--

#The course details:
<?php
foreach($course['Course'] as $field => $value) {
    if(in_array($field, array('user_id','active','approved','approval_token','mod_mailed',
        'last_reminder','course_parent_type_id','skip_info_url','skip_guide_url',
        'contact_mail','contact_name','lon','lat'))) continue;
    // handle foreign keys
    if(strpos($field, '_id', -3) !== false) {
        $model = Inflector::camelize(substr($field, 0, strlen($field) - 3));
        $value = $course[$model]['name'];
    }
    echo $field.': \t\t'.$value.'\n';
}
echo '\n#Disciplines:\n';
foreach($course['Discipline'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
echo '\n\n#Techniques\n';
foreach($course['TadirahTechnique'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
echo '\n\n#Objects\n';
foreach($course['TadirahObject'] as $k => $item) {
    if($k > 0) echo ', ';
    echo $item['name'];
}
?>