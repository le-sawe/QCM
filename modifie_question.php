<?php 
    session_start();
    session_regenerate_id();
    if(!isset($_SESSION['username']) || $_SESSION['user_type'] != 2){header("Location: authentication_page.php");}// if there is no valid session
    include('connection.php');  // connect to the database

    $langage_sql = "SELECT idlangage, nomlangage FROM langage";
    $result_langage = $conn->query($langage_sql);

    // GET SECTION

    if ($_SERVER['REQUEST_METHOD'] === 'GET' ) {// its a post request ? if yes continue
        if (isset($_GET["modification_question_id"])){
            // Get the question id
            $question_id_for_modification =  $_GET["modification_question_id"];
            //Get initial data
        
            $initial_data=get_initial_data($question_id_for_modification);
            $initial_question = $initial_data[0];
            $initial_niveau = $initial_data[1];
            $initial_maker = $initial_data[2];
            $initial_langage = $initial_data[3];
            $initial_reponse_data = $initial_data[4];
            $initial_reponse_number = $initial_data[5];
            $correct_reponse_nombre = $initial_data[6];
            
        }
    }

    // POST SECTION

    if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {// its a post request ? if yes continue
        $erorr = 0;// if there is no error at the end we will print update success ...
        
        //Get data from post request
        $laquestion = $_POST["question"];// Get the question
        $niveau = $_POST["niveau"];// Get the level
        $language_id = $_POST["language_id"]; // Get the langauge
        $reponse_nombre = $_POST["reponse_nombre"]; // number of reponse 3 4 5
        $reponse_correct = $_POST["reponse_correct"]; // the one correct
        $question_id_for_modification = $_POST["question_id_for_modification"];//get the question id
    
        //Get initial data

        $initial_data=get_initial_data($question_id_for_modification);
        $initial_question = $initial_data[0];
        $initial_niveau = $initial_data[1];
        $initial_maker = $initial_data[2];
        $initial_langage = $initial_data[3];
        $initial_reponse_data = $initial_data[4];
        $initial_reponse_number = $initial_data[5];
        $correct_reponse_nombre = $initial_data[6];

    
        
    
        $username =$_SESSION['username']; // get the username from the session
        $idadmin =$_SESSION['username'];
    
        // Check for empty field
        $not_accepted =0 ;// if its 0 ----> accepted , 1 -----> not accepted
        if(empty($laquestion) || empty($niveau) || empty($niveau)){$not_accepted =1 ;} // if any of those fields is empty
        // Check if any response have a null value
        for($i=1 ; $i<$reponse_nombre+1;$i++){
            $reponse = $_POST["reponse_nombre_$i"];
            if(empty($reponse)){$not_accepted =1 ;}
        }
    
        // check if the maker of this question is same who want to edit it
        if($idadmin != $initial_maker){
            $not_accepted =1 ;
            echo "<h4 style='color=red'> seul celui qui crée cette question peut la modifier :) </h4>";
        }
    
        if($not_accepted ==0){// if its acepted   
    
            // Update the question 
            $question_update_sql = "UPDATE question SET enonce ='$laquestion' , niveau = '$niveau', idlangage = '$language_id' WHERE noquestion ='$question_id_for_modification'";
            if (!($conn->query($question_update_sql) === TRUE)) {
                echo "Error: " . $question_update_sql . "<br>" . $conn->error;
                $erorr = 1;
            } 
                    
            // Update each reponse
            for($i=1 ; $i<$reponse_nombre+1;$i++){
                $instant_reponse_id =$initial_reponse_data[$i-1][0];
                $correct = 0;
                if($i==$reponse_correct){$correct=1;}
                $reponse = $_POST["reponse_nombre_$i"];
                $reponse_update_sql = "UPDATE reponse SET texte ='$reponse' , correct = '$correct', noquestion = '$question_id_for_modification' WHERE noreponse ='$instant_reponse_id'"; 
                if (!($conn->query($reponse_update_sql) === TRUE)) {
                    echo "Error: " . $reponse_update_sql . "<br>" . $conn->error;
                    $erorr = 1;
                } 
            }
            if ($erorr == 0){
                //Get initial data to display the updated data
        
                $initial_data=get_initial_data($question_id_for_modification);
                $initial_question = $initial_data[0];
                $initial_niveau = $initial_data[1];
                $initial_maker = $initial_data[2];
                $initial_langage = $initial_data[3];
                $initial_reponse_data = $initial_data[4];
                $initial_reponse_number = $initial_data[5];
                $correct_reponse_nombre = $initial_data[6];
               echo "<h4 style='width:100%;' class='badge bg-success mx-0 my-0'>Succès</h4>";
            }
    
        }else{// if its not accepted
            echo "<h4 style='color:red'>!! Il y a un champ vide !!</h4>";
            
        }
    }
    function get_initial_data($question_id_for_modification){
        include('connection.php');  // connect to the database
        if(!empty($question_id_for_modification)){ // Check if there is a value in the question_id_for_modification
            //Get quesiton data 
            $get_question_fields_sql = "SELECT * FROM question WHERE noquestion  ='$question_id_for_modification'"; 
            $result_of_question_fields = $conn->query($get_question_fields_sql);
            if ($result_of_question_fields->num_rows > 0) {
                while($row_of_question_fields = $result_of_question_fields->fetch_assoc()) {
                    $initial_question=$row_of_question_fields["enonce"];
                    $initial_niveau=$row_of_question_fields["niveau"];
                    $initial_maker=$row_of_question_fields["idadmin"];
                    $initial_langage=$row_of_question_fields["idlangage"];
                }
            }
            //Get reponses data
            $get_reponse_fields_sql = "SELECT * FROM reponse WHERE noquestion  ='$question_id_for_modification'"; 
            $result_of_reponse_fields = $conn->query($get_reponse_fields_sql);
            if ($result_of_reponse_fields->num_rows > 0) {
                $initial_reponse_data=array();
                $initial_reponse_number = 0;
                while($row_of_reponse_fields = $result_of_reponse_fields->fetch_assoc()) {
                    $initial_reponse=$row_of_reponse_fields["texte"];
                    $initial_situation=$row_of_reponse_fields["correct"];
                    $no_reponse = $row_of_reponse_fields["noreponse"];
                    array_push($initial_reponse_data,array($no_reponse,$initial_reponse,$initial_situation,$question_id_for_modification));
                    $initial_reponse_number = $initial_reponse_number +1;  
                }
                $correct_reponse_nombre = 0 ;
                for($i=0;$i<sizeof($initial_reponse_data);$i++){
                    if($initial_reponse_data[$i][2]==1){$correct_reponse_nombre = $i+1;}
                }
            }
        }
        else{
            echo "<h4 style='color:red'>je ne peux pas savoir la question que vous voulez modifier</h4>";
        }
        return array($initial_question,$initial_niveau,$initial_maker,$initial_langage,$initial_reponse_data,$initial_reponse_number,$correct_reponse_nombre);
    }
?>

<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Modifier la Question</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/navbar.css" rel="stylesheet">
    </head>


<body class="text-center">
        

    <nav class="navbar navbar-expand navbar-dark bg-dark" >
        <div class="container-fluid">
        <a class="navbar-brand" href="#">Modifier la Question </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample02">
            <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link " href="adminhome.php">Liste des Questions</a>
            </li>     
            </ul>      
            <form method='get' class="mx-4" action='log_out.php'><button type='submit' class="btn btn-outline-danger">Déconnecter</button></form> 
        </div>
        </div>
    </nav>

    <br>
  
   
    <form method="post" action="modifie_question.php" class="mx-auto" style="max-width:500px">

    <!--Question -->
        <h4>La Question :</h4>    
        <hr>
        <div class="mb-3">
            <label  class="form-label">Langugae</label>
            <select class="form-select" name="language_id">
                    <?php  
                        if ($result_langage->num_rows > 0) {
                        while($row_langage = $result_langage->fetch_assoc()) {
                            $selected='';
                            if($row_langage["idlangage"]==$initial_langage){$selected="selected";}
                            echo '<option '. $selected.' value='. $row_langage["idlangage"].'>'. $row_langage["nomlangage"].'</option>';
                        }} 
                    ?>
                   
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nivau</label>
            <select class="form-select" name="niveau">
                <option <?php if($initial_niveau == 1){echo "selected";} ?> value='1'>Premier niveau</option>
                <option <?php if($initial_niveau == 2){echo "selected";} ?> value='2'>Deuxiem niveau</option>
                <option <?php if($initial_niveau == 3){echo "selected";} ?> value='3'>Troisiem niveau</option>
                
            </select>
        </div>
        <div class="mb-3">
            <label >La Question</label>
            <input class="form-control" name="question" placeholder="n'oublie pas le point d'interrogation" value="<?php echo $initial_question ;?>"></input>
        </div>
        <br>
        <br>

        <!--Reponse -->
        
        <h4>Les Reponse :</h4>    
        <hr>
        <br>
        <div class="mb-3">
            <span class=''>Préciser le nombre des choix :</span>
            
            <button type="button" class="btn btn-outline-primary" onclick="nombredesreponse(3);"> 3 </button>
            <button type="button" class="btn btn-outline-primary" onclick="nombredesreponse(4);"> 4 </button>
            <button type="button" class="btn btn-outline-primary" onclick="nombredesreponse(5);"> 5 </button>
            <br>
            <span class="badge bg-danger">le numéro de réponse initial était <?php echo $initial_reponse_number?></span>
            <br>
            <hr class="mx-5">
            <br>
            <div id="reponsearea">

            </div>
            <input type="hidden" name="reponse_nombre" id ='reponse_nombre' value=0;>
            <input type="hidden" name="question_id_for_modification"  value='<?php echo $question_id_for_modification ?>';>

        </div>

        <br>
        <br>

        <!--Le Choix Correct-->
        
            <div class="mb-3">
                <h4>Le Choix Correct est :</h4>
                <hr>
                <select id='choi_correct' name='reponse_correct' class="form-select bg-success text-white " aria-label="Default select example">
                </select>
            </div>
            <hr class="mx-5">
            <div id='submit'>

            </div> 
    </form>
    <script type="text/javascript">
        var initial_reponse_data = <?php echo json_encode($initial_reponse_data); ?>;
        nombre_des_reponse =    <?php echo $initial_reponse_number?>;            
        reponsearea= document.getElementById("reponsearea");
        choi_correct= document.getElementById("choi_correct");
        reponse_nombre = document.getElementById("reponse_nombre");
        submit = document.getElementById("submit");           
        submit.innerHTML='<button type="submit" class="btn btn-primary">Submit</button>   ';           
        
        reponsearea.innerHTML='';
        for(i =1 ; i< nombre_des_reponse+1 ; i++){
                reponsearea.innerHTML +='<br>Le Choix nombre '+i+' :<input name="reponse_nombre_'+i+'" class="form-control" value="'+initial_reponse_data[i-1][1]+'"  ></input><div id="reponsearea">';              
            }
            
        choi_correct.innerHTML='';
        for(i =1 ; i< nombre_des_reponse+1 ; i++){
                selected_choi = '';
                if(i == <?php echo $correct_reponse_nombre ?>){selected_choi="selected";}
                choi_correct.innerHTML +='<option '+selected_choi+' value="'+i+'">Le Choix nombre '+i+'</option>   ';           
                }
                reponse_nombre.value = nombre_des_reponse;
        
        function nombredesreponse (nombre_des_reponse){
            submit.innerHTML='<button type="submit" class="btn btn-primary">Submit</button>   ';
            reponsearea.innerHTML='';
            for(i =1 ; i< nombre_des_reponse+1 ; i++){
                reponsearea.innerHTML +='<br>Le Choix nombre '+i+' :<input name="reponse_nombre_'+i+'" class="form-control"  ></input><div id="reponsearea">';              
            }
            choi_correct.innerHTML='';
            for(i =1 ; i< nombre_des_reponse+1 ; i++){
                choi_correct.innerHTML +='<option value="'+i+'">Le Choix nombre '+i+'</option>   ';           
                }
            reponse_nombre.value = nombre_des_reponse;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

  
 
 
</html>