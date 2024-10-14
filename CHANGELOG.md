## Changelog ##
[[v1.5.2]] 20241014
fix: missing icon to indicate if table is collapsed or unfold in boost in moodle 4.0.5

[[v1.5.1]] 20240120
Reviewed version of pull request with some adaptions.
1. make separator in heading configurable by css eg change margin between rolelocalname and separator
2. default is not needed in langstring because there is a new heading for the section with the default settings
3. display the tools in a seperate row
4. add folder icon in front of coursecategoryname
5. add calender icon in front of duration
6. add langstring (category hidden) to the plugin instead of using core lang string 
7. add indicator for roles that are not core roles and add some css for backgroundcolor for non core roles
8. add backgroundcolor in css for all core roles and also for all non core roles
9. add icon that indicates when courselist is collapsed and add hover effect to show where to show or hide courselist

[[v1.5.0]] 20240120
add pullrequest from eLearning-TUDarmstadt

1. A top level category can be shown for the courses in table
- this is disabled by default, configurable and can be used with/ instead of/ without duration time ranges depending on users' preference
- moodle/category:viewhiddencategories permission is respected: category names are only displayed when they are visible or when the user is allowed to see them in course context, otherwise get_string('categoryhidden') is returned

2. Set background color of non-standard roles to #eee (equal to guest color) instead of white for better contrast
- the solution is not very clean and uses CSS wildcards, so we could modify or remove this part as needed

3. Added : between role name and amount of courses for readability, for example "Manager : 1 Course"

4. Simplified some expressions in code
 
5. Set required Moodle version to 4.0


[[v1.4.2]] 20240106  
- changes in css and template
- add sr-only text for icon eye
- add heading to the default websitesettings and change the langstrings by removing the word Default:

[[v1.4.1]] 20230311

- new feature: show how many courses a user is enroled with a role
- fix spacing problems with the agenda
- new position for the icons that indicates if a course is hidden

[[v1.4.0]] 20221211  
- new feature: fold or unfold courselist on start

[[v1.3.0]] 20221210  
- new feature: new default setting to only show favourite courses  
- fix for S. H.: do not show roleshortname in title  

[[v1.2.0]] 20221129  
- new feature: default setting can be configured if courses ended in the past, are in progress or start in the future should be shown by default  
- default settings are stored when adding the block to a course  

[[v1.1.1]] 20221127  
- new feature: block can be configured which courses should be shown (course ended in the past, are in progress or start in future)  

[[v1.0.1]] 20221127  
- set to MATURITY_STABLE  
- fix: switch dimmed and not dimmed in mustache  

[[v0.0.5]] 20221125  
- only show rolesections if user is enrolled at least in one course with this role  
- substitute hard coded langstrings  

[[v0.0.4]] 20221125  
- bump version  
- README.md  

[[v0.0.3]] 20221125  
- codeing improvements  

[[v0.0.2]] 20221122  
- layout improvement  
- add missing capability  
- codeing improvements  

[[v0.0.1]] 20221115  
- initial alpha version  
