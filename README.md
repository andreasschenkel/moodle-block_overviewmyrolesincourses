# moodle-block_overviewmyrolesincourses


A: How to use 

B: Settings 

C: Capabilitys

D: Changelog 

E: Installing via uploaded ZIP file

F: Installing manually


## A: How to use ##

Block that shows a list of all courses a user is enrolled .


## B: Settings ##

- block_overviewmyrolesincourses | isactiv (set true, the block is activated)  
- block_overviewmyrolesincourses | supportedroles (Siteadmin can select the roles that should be supported from a list of roles that can be assigned in coursecontext.)  
- block_overviewmyrolesincourses | showdeleteicon (If a user has the capability  moodle/course:delete then a delete icon is shown)  


## C: Capabilitys ##

   C1: 'myaddinstance'  
   capability to add the block at the my-page
   
   C2: 'addinstance'
   capability to add the block at the course-page
   


## E: Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## F: Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/overviewmyrolesincourses

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
