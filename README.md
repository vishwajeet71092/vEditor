# V Editor
This is a next version of ftp-editor (https://github.com/vishwajeet71092/FTP-Editor) written in php, js, html and css. 

## About This Project
###### Plugin Name: V Editor
###### Plugin version: 1.0.0
###### Plugin Author: vishwajeet kumar
###### Description: An online code editor with filemanager which will help web developers to code or edit a project online.

## Minimum Requirement
1. FTP Access To Upload the File.
2. Linux OS and Apache Server with PHP.
3. Use firefox, as in chrome it will give some error (to save xss attack) while saving a file which has js in it.

## Feature

FILE MANAGER
	1. rename file
	2. rename folder
	3. create file
	4. create folder
	5. delete file
	6. delete folder
	7. upload file

TEXTEDITOR
	1. Edit file
	2. Save File
	3. find and replace
	4. list directory
	5. open and close dir in sidebar
	6. Name of current editing File
	7. select document type
	8. select font size
	9. select theme
	10. Clt + S (save Enabled)
	11. open folder icon
	12. set editor according to document type (php css script html text sass less)
	13. cookie for font and theme
	14. Lots of Keyboard Shortcuts (press clt+alt+h to view Keyboard Shortcuts)
	15. Emmit Enabled (cheatcode: https://docs.emmet.io/cheat-sheet/)

Miscellaneous
	1. login page with session
	2. logout
	3. set working directory
	4. reset working directory

## How to use
1. This Editor comes in two form (i). one file with CDN (ii) one folder without CDN 
2. Download any one of these two
2. upload it on server
3. Access with url {your domain name}/v-editor.php or {your domain name}/vEditor/v-editor.php
4. Password is **password**
5. steps to change the password
   5.1 convert your new passwordin md5 hash
   5.2 find the below code in v-editor.php

   ````html
    if(md5($_POST['password']) == '5f4dcc3b5aa765d61d8327deb882cf99'){
    ````
    5.3 replace **5f4dcc3b5aa765d61d8327deb882cf99** with new hash

## screenshot

![alt text](https://raw.githubusercontent.com/vishwajeet71092/vEditor/master/screenshot/login.PNG)

![alt text](https://raw.githubusercontent.com/vishwajeet71092/vEditor/master/screenshot/base.PNG)

![alt text](https://raw.githubusercontent.com/vishwajeet71092/vEditor/master/screenshot/editor.PNG)

![alt text](https://raw.githubusercontent.com/vishwajeet71092/vEditor/master/screenshot/filemanager.PNG)