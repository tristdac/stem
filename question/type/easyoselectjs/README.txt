  Moodle 2.3+ plugin: EasyOChem MarvinJS Selection question type

INSTALLATION:

This will NOT work with Moodle 2.0 or older, since it uses the new
question API implemented in Moodle 2.1.

This is a Moodle question type. It should come as a self-contained 
"easyoselectjs" folder which should be placed inside the "question/type" folder
which already exists on your Moodle web server.

You must download a recent copy of MarvinJS from www.chemaxon.com.  You can
set the path to MarvinJS in the admin panel.


USAGE:

With the MarvinJS Selection question type the instructor draws a structure,
set of structures or reactions and selects certain objects (atoms, molecules etc).
The student must then select the same objects.  You can ask questions such as
"Select all chiral centers in the following structures?" or "Choose the nucleophile 
in the following reaction?"
