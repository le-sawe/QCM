<?php 
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["user_type"]) && isset($_POST["username"]) && isset($_POST["password"])) {// if request method is POST
    include('connection.php');      // connect the database
    if(!(empty($_POST["user_type"]) || empty($_POST["username"]) || empty($_POST["password"]) ) ){
      //GEt the data from the form
      $user_type = $_POST["user_type"];
      $username = $_POST["username"];
      $password = $_POST["password"];
      echo "user type : ".$user_type."<br>username : ".$username."<br><br>password : ".$password."<br>";
      if($user_type==1){ // Abonne
        echo "abonne";
        $auth_sql = "select *from abonne where username = '$username' and password = '$password'";          
        $result = mysqli_query($conn, $auth_sql);  
        $count = mysqli_num_rows($result); 
        if($count == 1){// if authenctication success  
            $user_data_sql = "SELECT * FROM abonne WHERE username = '$username'";
            $user_data_result = $conn->query($user_data_sql);
            if ($user_data_result->num_rows > 0) {
              while($user_data_row = $user_data_result->fetch_assoc()) {
                $_SESSION['idabonne'] = $user_data_row["idabonne"];
                $_SESSION['username'] = $username;
                $_SESSION['user_type'] = 1;
                $_SESSION['password'] = $password;  
                $_SESSION['nom'] = $user_data_row["nom"];
                $_SESSION['prenom'] = $user_data_row["prenom"];
              }
            }
            header("Location: abonnee.php");
          }
        }
        elseif($user_type==2){ // Admin
          echo "its admin";
          $auth_sql = "select *from admin where username = '$username' and password = '$password'";          
          $result = mysqli_query($conn, $auth_sql);  
          $count = mysqli_num_rows($result); 
          
          if($count == 1){// if authenctication success  
            echo "its admin success";
              $user_data_sql = "SELECT * FROM admin WHERE username = '$username'";
              $user_data_result = $conn->query($user_data_sql);
              if ($user_data_result->num_rows > 0) {
                while($user_data_row = $user_data_result->fetch_assoc()) {
                  $_SESSION['idadmin'] = $user_data_row["idadmin"];
                  $_SESSION['username'] = $username;
                  $_SESSION['user_type'] = 2;
                  $_SESSION['password'] = $password;  
                  $_SESSION['nom'] = $user_data_row["nom"];
                  $_SESSION['prenom'] = $user_data_row["prenom"];
                }
              }
              header("Location: adminhome.php") ;
          }   
      }    
    } 
    else{  
        echo "<h1> Login failed. Invalid username or password.</h1>";  
    }     
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Authenticaiton page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
  <form method="POST" action="authentication_page.php">
    <img class="mb-4" src="img/omencodes.png" alt=""  height="100">
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
    <!-- User Type input  -->
    <div class="form-floating">
    <select class="form-select"  name='user_type'>
        <option value='1' selected>Abonne</option>
        <option value="2">Admin</option>     
    </select>
      <label for="floatingInput">User Type</label>
    </div>
    <!-- Username input  -->
    <div class="form-floating">
      <input type="text" class="form-control" id="floatingInput" placeholder="Username" name='username' >
      <label for="floatingInput">Username</label>
    </div>
    <!-- Password input  -->
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name='password'>
      <label for="floatingPassword">Password</label>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2017–2021</p>
</form>

</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
  <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
</html>

