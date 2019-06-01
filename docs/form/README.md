# Form Class

Generate nicely formatted input forms

## Attributes

Form::db - link to PDO object created in includes/thpsecurity.php

## Basic Methods

$form=new Form;

$form->start($db, $action=/update); // Set up an aligned form, method=POST,

$form->hidden($array); // assoc array of preset fields to hide 

$form->record($table,$id); // edit this record

$form->end($submit_button_message=”Save Data”); 

## Advanced Methods

Note: either the current or default values are in $_SESSION[$name]. Validation is by HTML5 instead of a javascript plugin.

->data($array); // load initial data into the form

->num($name,$min,$max); // Optional numeric limits

->date($name,$min,$max); // Optional limits on pop-up calendar

->text($name);

->textarea($name);

->pairs($name,$assoc_array); // simple dropdown based on an array

->query($name,$query that results in pairs);

