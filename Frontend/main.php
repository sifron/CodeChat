<?php
	require_once('../cookie.php');
?>

<!DOCTYPE html>
<html>
    
    <head>
        <title>CodeChat</title>
        <link rel="stylesheet" href="main.css">
        <script type="text/javascript" src="http://www.cs.unc.edu/Courses/comp426-f15/jquery-1.11.3.js"></script>
        <script type="text/javascript" src="chatapp.js"></script>
        <script type="text/javascript" src="http://sifronb.com/ckeditor/ckeditor.js"></script>
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/styles/monokai_sublime.min.css">
<script src="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>
<!--        <script>hljs.initHighlightingOnLoad();</script>-->
    </head>
    <body>
        <div id="navbar">
            <div id="groupbutton"></div>
            <div id="settingsbutton"></div>
            <div class="logo">CodeChat</div>
        </div>
        <div id="groupcontainer">
            <h3>Groups</h3>
            <div id="createGroup">+</div>
            <div id="grouplist">
            	<!--<div class="group">Group 1</div>-->
            	<!--<div class="group">Group 2</div>-->
            	<!--<div class="group">Group 3</div>-->
            	<!--<div class="group">Group 4</div>	-->
            	<!--<div class="group">Group 5</div>-->
            	<!--<div class="group">Group 6</div>-->
            </div>
        </div>
        <div id="overlay"></div>
        <div id="codeprompt">
<!--
            <div id="codesettings">
                <p>Language: </p>
                <select id="langselect">
                    <option value="" selected>Autodetect</option>
                    <option value="nohighlight">No Highlight</option>
                    <option value="cs">C#</option>
                    <option value="cpp">C++</option>
                    <option value="css">CSS</option>
                    <option value="dos">DOS</option>
                    <option value="django">Django</option>
                    <option value="html">HTML</option>
                    <option value="http">HTTP</option>
                    <option value="json">JSON</option>
                    <option value="java">Java</option>
                    <option value="javascript">JavaScript</option>
                    <option value="matlab">Matlab</option>
                    <option value="objectivec">Objective C</option>
                    <option value="php">PHP</option>
                    <option value="powershell">PowerShell</option>
                    <option value="python">Python</option>
                    <option value="k">Q</option>
                    <option value="r">R</option>
                    <option value="ruby">Ruby</option>
                    <option value="sql">SQL</option>
                    <option value="stata">Stata</option>
                    <option value="swift">Swift</option>
                    <option value="xml">XML</option>
                    <option value="xpath">XQuery</option>
                </select>
            </div>
-->
            <form id="CKEcode">
                <textarea id="codeinput" name="codeinput"></textarea>
                <script>
                    CKEDITOR.replace('codeinput', {
                        removePlugins: 'toolbar'});
                    CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
                    CKEDITOR.config.height = 475;
                </script>
                <div id=centerbtn><button id="codeSend">Send</button></div>
            </form>
        </div>
        <div id="newGroup">
        	<div id="creategroupheader">Create New Group</div> <br>
        	<input type="text" id="newGroupName" placeholder="Group Name"> <br>
        	<input type="text" id="newGroupDesc" placeholder="Description (optional)"> <br>
        	<input type="text" id="newGroupGit" placeholder="Github (optional)"> <br>
        	<button id="newGroupSubmit">Create</button>
        </div>
        
        <div id="newMember">
        	<div id="creatememberheader">Add Member To Group</div> <br>
        	<input type="text" id="newMemberEmail" placeholder="Email Address"> <br>
        	<button id="newMemberSubmit">Add</button>
        </div>
        
        <div id="accountPrefs">
        	<div id="accountpreferencesheader">Account Preferences</div> <br>
        	<input type="text" id="newName" placeholder="Name"> <br>
        	<input type="text" id="newEmail" placeholder="Email Address"> <br>
        	<!--<input type="text" id="newPassword" placeholder="Password"> <br>-->
        	<input type="text" id="newGit" placeholder="Github"> <br>
        	<button id="accountPrefSubmit">Save</button>
        </div>
        
        <div id="chatcontainer">
        	<div id="addMember">+</div>
        	<div id="groupname"></div>
        	<div id="messageContainer">
            <div id="messageList">
            	
            </div>
            </div>
            <form id="chat">
                <div id="chatborder">
                    <div id="innercontainer">
                        <input type="text" name="chatInput" id="chatInput" placeholder="Message" autocomplete="off" size="1">
                        <button id="chatSend">Send</button>
                        <button id="codeWin">&lt;/&gt;</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="settingscontainer">
        	<div class="settingoption">Account Preferences</div>
        	<div id="signoutbtn" class="signoutlink">Sign Out</div>
        </div>
    </body>
    
</html>