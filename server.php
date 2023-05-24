<?php
session_start();
ob_start();

// initializing variables
$errors = array();
$servername = "localhost"; 
    $hostusername = "id20648866_admin"; 
    $hostpassword = "Nikhil@910";
    $database = "id20648866_doonbank";
    
    $mysqli = @new mysqli($servername, $hostusername, $hostpassword, $database);
    
// connect to the database
$db =  mysqli_connect($servername, 
         $hostusername, $hostpassword, $database);
         if(isset($_SESSION['username'])){
         $username=	$_SESSION['username'];
$query= "SELECT * FROM user WHERE username= '$username' ";
    $results=mysqli_query($db, $query);
    $row=mysqli_fetch_assoc($results);
    $_SESSION['accno']= $row["Account_Number"];
    $_SESSION['amount']= $row["amount"];
    if($row["gender"]== 1)
    {
        $_SESSION['gender']= "Female";
    }
    else{
    $_SESSION['gender']= "Male";
    }
    
    $_SESSION['address']= $row["address"];
    $_SESSION['phone']= $row["phone"];
    
$accno= $_SESSION['accno'];
    $amount=  $_SESSION['amount'];
    $gender = $_SESSION['gender'];
    $address = $_SESSION['address'];
    $phone = $_SESSION['phone'];
}

// REGISTER USER
if (isset($_POST['reg_user']) || isset($_POST['reg_admin'])) {
  // receive all input values from the form

 
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password']);
  $password_2 = mysqli_real_escape_string($db, $_POST['repassword']);
  $gender= mysqli_real_escape_string($db,$_POST['gender']);
  $amount= mysqli_real_escape_string($db, $_POST['amount']);
  $address= mysqli_real_escape_string($db, $_POST['address']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);


  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM user WHERE Username='$username' OR Phone='$phone' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] == $username) {
      array_push($errors, "Username already exists");
      echo '<script>alert("Username already exists")</script>';
    }

    if ($user['phone'] == $phone) {
      array_push($errors, "Phone Number already exists");
      echo '<script>alert("Phone Number already exists")</script>';
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$userpassword = md5($password_1);//encrypt the password before saving in the database
	$accno=rand(100000,100099);
  	$query = "INSERT INTO user (Account_number,username, gender,password ,amount,address,phone) 
  			  VALUES('$accno','$username', '$gender', '$userpassword','$amount','$address','$phone')";
  			  for(;;){
  			  if(!mysqli_query($db, $query)){
  			    $accno=rand(100000,100099);  
  			    $query = "INSERT INTO user (Account_number,username, gender,password ,amount,address,phone) 
  			  VALUES('$accno','$username', '$gender', '$userpassword','$amount','$address','$phone')";
  			  }
  			  else
  			  break;
  			  }
  	echo '<script>alert("Account Opened Successfully! Welcome to Bank Of Rishikesh.")</script>';
  	if (isset($_POST['reg_user'])){
  	unset($_SESSION['username']);
    unset($_SESSION['admin']);
  	$_SESSION['username'] = $username;
	$_SESSION['Account_number']=$accno;
  	echo ("<SCRIPT LANGUAGE='JavaScript'>
  window.location.href='welcomeUser.php';
 </SCRIPT>");
  	}
  	else{
  	    echo '<script>alert(" Account of '.$username.' opened with Account Number '.$accno.'")</script>';
  	    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='welcomeAdmin.php';
        </SCRIPT>");
  }
}
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username =  $_POST['username'];
  $userpassword =  md5($_POST['password']);
$query = "SELECT * FROM user WHERE username='$username' AND password='$userpassword'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
  	{
  	 $row= mysqli_fetch_assoc($results);
    unset($_SESSION['username']);
    unset($_SESSION['admin']);
    $_SESSION['username']= $username;
  	      $_SESSION['accno']= $row["Account_Number"];
  	    echo ("<SCRIPT LANGUAGE='JavaScript'>
  window.location.href='welcomeUser.php';
 </SCRIPT>");
 

  	}
 else {
  		echo' <script> 
  		alert("WRONG USERNAME/PASSWORD COMBINATION");
  		</script>';
  	}
  }


// LOGIN ADMIN
if (isset($_POST['login_admin'])) {
  $username =  $_POST['username'];
  $adminpassword =  md5($_POST['password']);
$query = "SELECT * FROM admin WHERE username='$username' AND password='$adminpassword'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
  	{
    unset($_SESSION['username']);
    unset($_SESSION['admin']);
  	    $_SESSION['admin']=$username;
  	    
  	    echo ("<SCRIPT LANGUAGE='JavaScript'>
  window.location.href='welcomeAdmin.php';
 </SCRIPT>");
  	}
 else {
  		echo' <script> 
  		alert("WRONG USERNAME/PASSWORD COMBINATION");
  		</script>';
  	}
  }


//ACCOUNT INFO for ADMIN
if (isset($_POST['getbalanceadmin'])) {
   
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    $accno= $_POST['accno'];
$query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1)
{
    $query= "SELECT * FROM user WHERE Account_number= '$accno' ";
    $results=mysqli_query($db, $query);
    if(mysqli_num_rows($results)==1)
    {
    $row=mysqli_fetch_assoc($results);
    $name=$row["username"];
    $accountno=$row["Account_Number"];
    $amount=$row["amount"];
    $getbal=true;
     header('location totalbalanceadmin.php'); 
} else{
    echo' <script> 
  		alert("Enter correct account number");
  		</script>';
}
}
else {
    $accno= "Cannot show account number. Select A valid Account first.";
    $amount= "Cannot show amount. Select a valid Account first.";
  		echo' <script> 
  		alert("WRONG USERNAME/PASSWORD COMBINATION");
  		</script>';
}
}

//Transfer User Money Module

if(isset ($_POST['truser'] ))
{
    $target= $_POST['taccountno'];
    $tamount= $_POST['tamount'];
    $password= md5($_POST['password']);
    
$query= "SELECT * FROM user WHERE Account_number='$accno' AND password='$password'";
$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1)
{ 
    if($tamount>$amount || $tamount<0)
    {
    echo' <script> 
  		alert("INSUFFICIENT FUNDS");
  		</script>';
    }
    
    else{
    $value=$amount-$tamount;
    $query= "UPDATE user SET amount='$value' WHERE Account_Number='$accno'";
    mysqli_query($db, $query);
    
    $query= "SELECT * FROM user WHERE Account_Number='$target' ";
    $results= mysqli_query($db,$query);
    $row=mysqli_fetch_assoc($results);
    $value= $row['amount']+$tamount;
     $query= "UPDATE user SET amount='$value' WHERE Account_Number='$target'";
    mysqli_query($db, $query);
    echo' <script> 
  		alert("  ₹'.$tamount.' transferred to Account Number '.$target.' !");
  		</script>';
    }
   
}
else{
    echo' <Script>
    alert("Incorrect Username/Password Combination");
    </script> ';
}
}

//Transfer ADMIN module
if(isset ($_POST['tradmin'] ))
{
    $account= $_POST['accountno'];
    $target= $_POST['taccountno'];
    $tamount= $_POST['amount'];
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
    {
$query= "SELECT * FROM user WHERE Account_number='$account' ";
$results = mysqli_query($db, $query);
$row= mysqli_fetch_assoc($results);
$query= "SELECT * FROM user WHERE Account_number='$target' ";
$results2 = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1 && mysqli_num_rows($results2) == 1 )
{ 
    if($tamount>$row['amount'] || $tamount<0)
    {
    echo' <script> 
  		alert("INSUFFICIENT FUNDS");
  		</script>';
    }
    
    else{
    $value=$row['amount']-$tamount;
    $query= "UPDATE user SET amount='$value' WHERE Account_Number='$account'";
    mysqli_query($db, $query);
    
    $query= "SELECT * FROM user WHERE Account_number='$target' ";
    $results= mysqli_query($db,$query);
    $row=mysqli_fetch_assoc($results);
    $value= $row['amount']+$tamount;
     $query= "UPDATE user SET amount='$value' WHERE Account_Number='$target'";
    mysqli_query($db, $query);
    echo' <script> 
  		alert("  ₹'.$tamount.' transferred to Account Number '.$target.' !");
  		</script>';
    
    }
}
else{
    echo' <script> 
  		alert("INVALID ACCOUNT NUMBER ENTERED!");
  		</script>';
}
}
else{
echo' <script> 
  		alert("WRONG USERNAME/PASSWORD COMBINATION");
  		</script>';
    
}
}

//DEPOSIT MODULE
if(isset ($_POST['deposit'] ))
{
    $account= $_POST['accountno'];
    $tamount= $_POST['amount'];
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
    {
        $query= "SELECT * FROM user WHERE Account_number='$account' ";
$results = mysqli_query($db, $query);
$row= mysqli_fetch_assoc($results);
if($tamount<=0)
    {
    echo' <script> 
  		alert("Enter Valid Amount");
  		</script>';
    }
    
    else{
    $value=$row['amount']+$tamount;
    $query= "UPDATE user SET amount='$value' WHERE Account_Number='$account'";
    mysqli_query($db, $query);
    
    echo' <script> 
  		alert("  ₹'.$tamount.' transferred to Account Number '.$account.' !");
  		</script>';
    
    }
}
else
{
    echo' <script> 
  		alert("INCORRECT USERNAME/PASSWORD COMBINATION");
  		</script>';
}
}


//WITHDRAW MODULE
if(isset ($_POST['withdraw'] ))
{
    $account= $_POST['accountno'];
    $tamount= $_POST['amount'];
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
    {
        $query= "SELECT * FROM user WHERE Account_number='$account' ";
$results = mysqli_query($db, $query);
$row= mysqli_fetch_assoc($results);
if($tamount>$row['amount'] || $tamount<0)
    {
    echo' <script> 
  		alert("Enter Valid Amount");
  		</script>';
    }
    
    else{
    $value=$row['amount']-$tamount;
    $query= "UPDATE user SET amount='$value' WHERE Account_Number='$account'";
    mysqli_query($db, $query);
    
    echo' <script> 
  		alert("  ₹'.$tamount.' withdrawn from Account Number '.$account.' !");
  		</script>';
    
    }
}
else
{
    echo' <script> 
  		alert("INCORRECT USERNAME/PASSWORD COMBINATION");
  		</script>';
}
}

//CLOSE ACCOUNT MODULE (any account) 
if(isset ($_POST['close'] ))
{
    $account= $_POST['accountno'];
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
    {
        $query= "SELECT * FROM user WHERE Account_number='$account' ";
$results = mysqli_query($db, $query);
$row= mysqli_fetch_assoc($results);
if (mysqli_num_rows($results) == 1)
{
    $query= "DELETE FROM `user` WHERE `user`.`Account_Number` = '$account'";
     mysqli_query($db, $query);
     echo' <script> 
  		alert("  Account Number '.$account.' Closed Successfully !");
  		</script>';
}
else
{
    echo' <script> 
  		alert("INVALID ACCOUNT NUMBER ENTERED!");
  		</script>';
}

}else
{
    echo' <script> 
  		alert("INCORRECT USERNAME/PASSWORD COMBINATION");
  		</script>';
}
}

//CLOSE ACCOUNT MODULE (current account) 
if(isset ($_POST['closeuser'] ))
{
    $account= $_POST['accountno'];
    $username= $_POST['username'];
    $password= md5($_POST['password']);
    $c_user=$_SESSION['username'];
    $query= "SELECT * FROM user WHERE username='$c_user";
    $results = mysqli_query($db, $query);
    $row= mysqli_fetch_assoc($results);
    if($row['username']!=$username || $row['Account_number']!=$account || $row['password']!=$password){
       echo' <script> 
  		alert("INVALID CREDENTIALS ENTERED!");
  		</script>'; 
    }
    else{
    
    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1)
    {
        $query= "SELECT * FROM user WHERE Account_number='$account' ";
$results = mysqli_query($db, $query);
$row= mysqli_fetch_assoc($results);
if (mysqli_num_rows($results) == 1)
{
    $query= "DELETE FROM `user` WHERE `user`.`Account_Number` = '$account'";
     mysqli_query($db, $query);
     echo' <script> 
  		alert("  Account Number '.$account.' Closed Successfully !");
  		</script>';
}
else
{
    echo' <script> 
  		alert("INVALID ACCOUNT NUMBER ENTERED!");
  		</script>';
}

}else
{
    echo' <script> 
  		alert("INCORRECT USERNAME/PASSWORD COMBINATION");
  		</script>';
}
}
}

//gettable
if(isset($_POST["gettable"])){
     $username= $_POST['username'];
    $password= md5($_POST['password']);
$query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1){
     $j=0;
    for($i=100000; $i<100100; ++$i){
    $query= "SELECT * FROM user WHERE Account_number='$i'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1){
    $rows[$j++]=mysqli_fetch_assoc($results);
    }
    }
}
else{
    echo'
    <Script>
    alert("WRONG USERNAME/PASSWORD COMBINATION");
    </Script>';
    
    echo ("<SCRIPT LANGUAGE='JavaScript'>
  window.location.href='totalbalanceadmin.php';
 </SCRIPT>");
}
}

?>
