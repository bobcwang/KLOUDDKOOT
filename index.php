<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?>
<?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["e"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>KloudKoot</title>
<style type="text/css">
body {
	margin: 0px;
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 14px;
}
/* PAGE TOP */
#pageTop {
	background:url(images/website_banner.png);
	width:1280px;
	height: 306px;
	margin-top:0px;
	top:0px;
}
#gg {
	width:100%;
	height:6px;
}
#pageTopWrap {
	width:100%;
	height:45px;
	margin-top:25px;
}
#login {
	text-align:right;
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function emptyElement(x){
	_(x).innerHTML = "";
}
function login(){
	var e = _("email").value;
	var p = _("password").value;
	if(e == "" || p == ""){
		_("status").innerHTML = "Fill out all of the form data";
	} else {
		_("loginbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = new XMLHttpRequest();
		ajax.open("POST","index.php",true);
		ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.onreadystatechange = function() {
	        if(ajax.readyState==4 && ajax.status== 200) {
	            if(ajax.responseText == "login_failed"){
					_("status").innerHTML = "Login unsuccessful, please try again.";
					_("loginbtn").style.display = "block";
				} else {
					window.location = "user.php?u="+ajax.responseText;
				}
	        }
        }
        ajax.send("e="+e+"&p="+p);
	}
}
</script>
</head>

<div id="pageTop">
  <div id="gg"></div>
  <div id="pageTopWrap">
    <div id="login">
      <form id="loginform" onsubmit="return false;">
        <label> Email </label>
        <input type="text" id="email" onfocus="emptyElement('status')" maxlength="88">
        <label> Password </label>
        <input type="password" id="password" onfocus="emptyElement('status')" maxlength="100">
        <button id="loginbtn" onclick="login()">Login</button>
        <p id="status"></p>
      </form>
      </p>
      <a href="forgot_password.php">forgot password</a>
      </p>
      <a href="sign_up.php">sign up</a> </div>
  </div>
</div>
<div id="pageMiddle"> </div>
<div id="pageBottom">&copy;KLOUDD PRODUCTIONS</div>
</body></html>
