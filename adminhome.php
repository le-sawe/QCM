
<?php 
session_start();
session_regenerate_id();
if(!isset($_SESSION['username']) || $_SESSION['user_type'] != 2){header("Location: authentication_page.php");}      // if there is no valid session
include('connection.php');  

// GET id and name of the languags
$langage_sql = "SELECT idlangage, nomlangage FROM langage";
$result_langage = $conn->query($langage_sql);

$filter_sql = "SELECT * FROM question ";
$filter_result = $conn->query($filter_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {// if its a post request
  // Delete Request   
  if (isset($_POST["delete_question_id"]) ){ 
      $delete_question_id = $_POST["delete_question_id"];
      $delete_question_sql = "DELETE FROM question WHERE noquestion='$delete_question_id'";
      if ($conn->query($delete_question_sql) === TRUE) {
      } else {
        echo "Error deleting record: " . $conn->error;
      }
    
      $delete_reponse_related_to_question_sql = "DELETE FROM reponse WHERE noquestion='$delete_question_id'";
      if ($conn->query($delete_reponse_related_to_question_sql) === TRUE) {
      } else {
        echo "Error deleting record: " . $conn->error;
      } 
  }
  // Filter Request
  if (isset($_POST["language_id"]) && $_POST["language_id"] != 0) {
    $language_id = $_POST["language_id"]; // Get the langauge
    $filter_sql = "SELECT * FROM question WHERE idlangage = '$language_id' ";
    $filter_result = $conn->query($filter_sql);
    }
  }  
  
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Admin Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/navbar.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <nav class="navbar navbar-expand navbar-dark bg-dark" >
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Liste des Questions </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample02">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="create_question.php">Créer une question</a>
          </li>
        </ul>
        <h4 class="mx-auto text-white">Salut <?php echo $_SESSION['nom']; ?> !</h4>
        <form method="post" action="adminhome.php" class="d-flex justify-content-evenly ">
          <button type="submit" class="btn btn-outline-info " style="border-top-right-radius : 0;border-bottom-right-radius : 0" >Filter</button>
        <select class="form-select" name="language_id" style="border-top-left-radius : 0;border-bottom-left-radius : 0">
        <option value='0' selected>ALL</option>
              <?php  
                  if ($result_langage->num_rows > 0) {
                  while($row_langage = $result_langage->fetch_assoc()) {
                      echo '<option '. $selected.' value='. $row_langage["idlangage"].'>'. $row_langage["nomlangage"].'</option>';
                  }} 
              ?>                    
            </select>
        </form>
        
        <form method='get' class="mx-4" action='log_out.php'><button type='submit' class="btn btn-outline-danger">Déconnecter</button></form>
      </div>
  
    </div>
  </nav>
 
<div >
<table class="table">
  <thead>
    <tr>
      <th scope="col">La Question</th>
      <th scope="col">Le Niveau</th>
      <th scope="col">La Laguage</th>
      <th scope="col">Faite par</th>
      <th scope="col">Modification</th>
      <th scope="col">Supprimé</th>
    </tr>
  </thead>
  <tbody>
  <?php         
      if ($filter_result->num_rows > 0) {
          // output data of each row
          while($row = $filter_result->fetch_assoc()) {

              $sql_username = "SELECT username FROM admin WHERE idadmin  ='{$row["idadmin"]}'"; 
              $result_username = $conn->query($sql_username);
              if ($result_username->num_rows > 0) {
                  while($row_username = $result_username->fetch_assoc()) {
                  $username=$row_username["username"];
                  }
              } 
              $sql_language = "SELECT nomlangage FROM langage WHERE idlangage  ='{$row["idlangage"]}'"; 
              $result_language = $conn->query($sql_language);
              if ($result_language->num_rows > 0) {
                  while($row_language = $result_language->fetch_assoc()) {
                  $langage=$row_language["nomlangage"];
                  }
              } 
            echo "<tr><th scope='row'>" . $row["enonce"]. "</th><td>" . $row["niveau"]. "</td><td>" . $langage. "</td><td>" . $username. "</td>";
            echo "
                  <td><form action='modifie_question.php' method='get'>
                  <input type='hidden' name = 'modification_question_id'value='" . $row["noquestion"]. "'>
                  <button type='submit' class='btn btn-outline-success'>Modifier</button>
                  </form></td>";
            echo "
                  <td><form action='adminhome.php' method='post'>
                  <input type='hidden' name = 'delete_question_id'value='" . $row["noquestion"]. "'>
                  <button type='submit' class='btn btn-outline-danger'>Delete</button>
                  </form></td></tr>";
          }
        } else {
          echo "0 results";
        }
    ?>
    
  </tbody>
</table>

    
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
 
</html>