  Moodle 2.3+ plugin: MarvinJS based Name to Structure (EasyOName) question type

   By Carl LeBlond


INSTALLATION:

This will NOT work with Moodle 2.0 or older, since it uses the new
question API implemented in Moodle 2.1.

This is a Moodle question type. It should come as a self-contained 
"easyonamejs" folder which should be placed inside the "question/type" folder
which already exists on your Moodle web server.

Once you have done that, visit your Moodle admin page - the database 
tables should automatically be upgraded to include an extra table for
the EasyOChem Name to Structure question type.

You must download a recent copy of MarvinJS from www.chemaxon.com (free for academic use).
Openbabel is also required.  The paths to these can be set in the admin panel.  

USAGE:

The Name to structure question can be used to design questions in whch you require students
to draw chemical structures or reactions schemes.
