<?php

if (!isset($_COOKIE['codechat_session'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/Frontend/login.html');
  exit();
}