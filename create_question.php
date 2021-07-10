<?php 
    session_start();
    session_regenerate_id();
    if(!isset($_SESSION['username']) || $_SESSION['user_type'] != 2) { header("Location: authentication_page.php");}     // if there is no valid session
    include('connection.php');  // connect to the database
    
    $langage_sql = "SELECT idlangage, nomlangage FROM langage";
    $result_langage = $conn->query($langage_sql);


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["question"]) && isset($_POST["niveau"]) && isset($_POST["language_id"]) && isset($_POST["reponse_nombre"]) && isset($_POST["reponse_correct"]) ) {// its a post request ? if yes continue
        $erorr = 0;
        
        //Get data from post request
        $laquestion = $_POST["question"];// Get the question
        $niveau = $_POST["niveau"];// Get the level
        $language_id = $_POST["language_id"]; // Get the langauge
        $reponse_nombre = $_POST["reponse_nombre"]; // number of reponse 3 4 5
        $reponse_correct = $_POST["reponse_correct"]; // the one correct

        // Check for empty field
        $not_accepted =0 ;// if its 0 ----> accepted , 1 -----> not accepted
        if(empty($laquestion) || empty($niveau) || empty($niveau)){$not_accepted =1 ;}
        for($i=1 ; $i<$reponse_nombre+1;$i++){
            $reponse = $_POST["reponse_nombre_$i"];
            if(empty($reponse)){$not_accepted =1 ;}
        }
        if($not_accepted ==0){// if its acepted        
            // Question part

            // Get the id of the username
            $idadmin=$_SESSION['idadmin'];
                                      
            // Create a question object in the data base 
            $sql = "INSERT INTO question (enonce, niveau, idlangage,idadmin)
            VALUES ('$laquestion',$niveau,$language_id,$idadmin)";  // i removed the dots here !!!!
            if (!($conn->query($sql) === TRUE)) {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $erorr = 1;
            } 
            $question_id = $conn->insert_id; // get the id of the question created (bcs its the last insert to the database on this point)
            
            //Reponse Part
            
            // Create each reponse
            for($i=1 ; $i<$reponse_nombre+1;$i++){
                $correct = 0;
                if($i==$reponse_correct){$correct=1;}
                $reponse = $_POST["reponse_nombre_$i"];
                $sql = "INSERT INTO reponse (texte,correct,noquestion)
                VALUES ('$reponse',$correct,$question_id)";  
                if (!($conn->query($sql) === TRUE)) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    $erorr = 1;
                } 
            }
            if ($erorr ==0){echo "<h5 class='badge bg-success text-white' style='width:100%;border-radius:0;'>La Question et les reponse créé avec succès </h5>";}
            $_SESSION['the_current_niveau']=$niveau;
            $_SESSION['the_current_language_id']=$language_id;
        }else{// if its not accepted
            $_SESSION['the_current_niveau']=$niveau;
            $_SESSION['the_current_language_id']=$language_id;
            echo "<h4 class='badge bg-danger text-white' style='width:100%;border-radius:0;>!! Il y a un champ vide !!</h4>";
        }
    }
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Créer une question</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/navbar.css" rel="stylesheet">
    </head>
    <head>
        <meta charset="utf-8">
        <title>Créer une question</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/navbar.css" rel="stylesheet">
    </head>


    <body class="text-center">

    <nav class="navbar navbar-expand navbar-dark bg-dark" >
        <div class="container-fluid">
        <a class="navbar-brand" href="#">Créer une question </a>
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
  

    <form method="post" action="create_question.php" class="mx-auto" style="max-width:500px">

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
                            if(isset($_SESSION['the_current_language_id']) && $row_langage["idlangage"]==$_SESSION['the_current_language_id'] ){$selected="selected";}
                            echo '<option '. $selected.' value='. $row_langage["idlangage"].'>'. $row_langage["nomlangage"].'</option>';
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
        <div class="mb-3">
            <label >La Question</label>
            <input class="form-control" name="question" placeholder="n'oublie pas le point d'interrogation" ></input>
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
            <hr class="mx-5">
            <br>
            <div id="reponsearea">

            </div>
            <input type="hidden" name="reponse_nombre" id ='reponse_nombre' value=0;>
        </div>

        <br>
        <br>
        <input type="hidden" name="question_id_for_modification"  value='<?php echo $question_id_for_modification ?>';>

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

    <script>
        reponsearea= document.getElementById("reponsearea");
        choi_correct= document.getElementById("choi_correct");
        reponse_nombre = document.getElementById("reponse_nombre");
        submit = document.getElementById("submit");
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

