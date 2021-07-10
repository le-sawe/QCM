
<?php 
session_start();
session_regenerate_id();
if(!isset($_SESSION['username']) || $_SESSION['user_type'] != 1){header("Location: authentication_page.php");}      // if there is no valid session

include('connection.php');  
//Get the language
$langage_sql = "SELECT idlangage, nomlangage FROM langage";
$result_langage = $conn->query($langage_sql);

$idabonne =$_SESSION['idabonne'];// get the id of the user
// Stock the language available in array and stock the level of every language for the user in array
$niveau_per_language =array();
$language=array();
$language_number =0;
if ($result_langage->num_rows > 0) {
    while($row_langage = $result_langage->fetch_assoc()) {        
        $language_number++;
        $idlangage = $row_langage['idlangage'];
        $langage_name = $row_langage['nomlangage'];
        $niveau_per_language_sql = "SELECT MAX(niveau) FROM test WHERE idabonne = '$idabonne' and idlangage='$idlangage' and (note=4 or note=5)" ;
        $result_niveau = $conn->query($niveau_per_language_sql); 
        $row_niveau = mysqli_fetch_array($result_niveau);
        $le_niveau = (int)$row_niveau[0] +1;
        if (empty($le_niveau)){$le_niveau=1;}
        array_push($niveau_per_language,$le_niveau);  
        array_push($language,$langage_name);        
    }} 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Abonne Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/navbar.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <nav class="navbar navbar-expand navbar-dark bg-dark" >
    <div class="container-fluid">
      <h4 class="mx-auto text-white">Salut <?php echo $_SESSION['nom']; ?> !</h4>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <form method='get' class="mx-4" action='log_out.php'><button type='submit' class="btn btn-outline-danger">Déconnecter</button></form>
    </div>
  </nav>
<div style="width:90%" class="mx-auto">
    <form method="post" action="test.php" class='mx-auto d-flex justify-content-evenly' style="width:500px;">
            <div class="mb-3">
                <label  class="form-label">Langugae</label>
                <select class="form-select" name="language_id">
                        <?php  
                        $langage_sql = "SELECT idlangage, nomlangage FROM langage";
                        $result_langage = $conn->query($langage_sql);
                            if ($result_langage->num_rows > 0) {
                            while($row_langage = $result_langage->fetch_assoc()) {                       
                                echo '<option  value='. $row_langage["idlangage"].'>'. $row_langage["nomlangage"].'</option>';
                            }} 
                        ?>
                    
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nivau</label>
                <select class="form-select" name="niveau">
                    <option <?php if(isset($_SESSION['the_current_niveau']) && $_SESSION['the_current_niveau'] == 1){echo "selected";} ?> value='1'>Premier niveau</option>
                    <option <?php if(isset($_SESSION['the_current_niveau']) && $_SESSION['the_current_niveau'] == 2){echo "selected";} ?> value='2'>Deuxiem niveau</option>
                    <option <?php if(isset($_SESSION['the_current_niveau']) && $_SESSION['the_current_niveau'] == 3){echo "selected";} ?> value='3'>Troisiem niveau</option>
                    
                </select>
            </div>
            <button class="btn btn-primary mt-4 mb-3  " >Commencer le test >></button>

    </form>
    <br>
    <br>
    <div>
        <h1 class='text-start' style="margin-right:40px;">Archives</h1>
        <hr class="">
        <div>
          <?php 
              for($i=0;$i<$language_number;$i++){
              
                $niveau_percent=((int)$niveau_per_language[$i]/3)*100;
              
                  echo '
                  <h4 class="text-start">'.$language[$i].' :</h4>            
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: '.$niveau_percent.'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Niveau '.$niveau_per_language[$i].'</div>
                  </div>
                  ';
              }

          ?>

        </div>
    </div>
    <br>
<br>
<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Date</th>
      <th scope="col">Langage</th>
      <th scope="col">Niveau</th>
      <th scope="col">Note</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $counter=0;
    $table_test_sql = "SELECT * FROM test WHERE idabonne='$idabonne'";
    $table_test_result = $conn->query($table_test_sql);
    if ($table_test_result->num_rows > 0) {
      $langage_sql = "SELECT  nomlangage FROM langage WHERE idlangage = '$idlangage'";
      $result_langage = $conn->query($langage_sql);
      // output data of each row
      while($table_test_row = $table_test_result->fetch_assoc()) {
        $langage_sql = "SELECT  nomlangage FROM langage WHERE idlangage = {$table_test_row['idlangage']}";
      $result_langage = $conn->query($langage_sql);
        if ($result_langage->num_rows > 0) {
          while($row_langage = $result_langage->fetch_assoc()) {
              $langage =$row_langage["nomlangage"];
          }} 
        $counter++;
        echo '
        <tr>
          <th scope="row">'.$counter.'</th>
          <td>'.$table_test_row["datetest"].'</td>
          <td>'.$langage.'</td>
          <td>'.$table_test_row["niveau"].'</td>
          <td>'.$table_test_row["note"].'</td>

        </tr>
          ' ;
      }
    } else {
      echo "0 results";
    }

   ?>
  </tbody>
</div>

</table>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
 
 
</html>

