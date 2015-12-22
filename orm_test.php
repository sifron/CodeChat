<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('orm/User.php');
require_once('orm/Group.php');
require_once('orm/Member.php');
require_once('orm/Message.php');
require_once('orm/Favorite.php');
require_once('orm/Authentication.php');
require_once('orm/DirectedMessage.php');


//$message = Message::find_by_id(85);
//$message->remove();

/*
$user = User::find_by_email('myemail@email.com');
$auth = Authentication::find_by_user_id($user->get_id());
$pass = 'my_password';

if ($auth->authenticate($pass)) {
	print("You're logged in <br>");
} else {
	print("Incorrect password <br>");
}*/

//$message = Message::create(7,2,"This is code",true);
//print($message->getJSON());

//$user->set_github('dbobbitt815');
//print($user->getJSON() . "<br>");

//$dmesg = DirectedMessage::create(1,6);
//$dmesg = DirectedMessage::find_by_message_id(1);
//print($dmesg->getJSON() . "<br>");

//$auth = Authentication::create(7,'my_password');
//print($auth->getJSON() . "<br>");
/*
$auth = Authentication::find_by_user_id(7);
print($auth->getJSON() . "<br>");
print($auth->get_id() . "<br>");
if ($auth->set_password('new_password', 'newer_password') == true) {
	print("yay, new password set <br>");
} else {
	print("boo <br>");
}*/

//$user = User::create("Test user 2", "fake@fake.com");
//Group::create("Comp426 Final", "This is the chat room for our project", "www.google.com");
//$user = User::find_by_id(1);
//print($user->getJSON() . "<br>");
//$user->remove();

//$group = Group::find_by_id(2);
//print($group->getJSON() . "<br>");
//$group->set_github_url("something.com");

/*
for ($i = 1; $i < 7; $i++) {
	$group = Group::find_by_id($i);
	print($group->getJSON() . "<br>");
}
*/

//print_r(Member::get_all_ids());

//$member = Member::create(6,1);
//print($member->getJSON() . "<br>");
/*
$message = Message::create(7,2,"Test message to group 2");
print($message->getJSON() . "<br>");

$message = Message::find_by_id(2);
print($message->getJSON() . "<br>");*/
//$message = Message::create(7,1,"Test message to group 1");
//$messages = Message::get_message_ids_of_user_in_group(7,2);
//print_r($messages);
//print("<br>");
//print(Message::find_by_id($messages[0])->get_text() . "<br>");
//print(Message::find_by_id($messages[1])->get_text() . "<br>");

/*
$messages = Message::get_all_ids();
$users = User::get_all_ids();

$fav = Favorite::create($messages[0], $users[0]);

print($fav->getJSON() . "<br>");


$favs = Favorite::get_favorited_message_ids_of_user_in_group(6,1);

if (sizeof($favs) == 0) {
	print("None found <br>");
} else {
	print_r($favs);
}*/







