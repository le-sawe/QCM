<?php 
session_start();
session_regenerate_id();
if(!isset($_SESSION['username']) || $_SESSION['user_type'] != 1){header("Location: authentication_page.php");} 
include('connection.php');  
     if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {// if its a post request
        
        if (isset($_POST["niveau"]) && isset($_POST["language_id"])){ // if its the initial request

            // reset the variable
            $_SESSION["the_5_question"]=0;
            $_SESSION["question_number"]=1;
            $_SESSION["list_des_reponse"]=0;
            $_SESSION["test_niveau"]=0;
            $_SESSION["test_langage"]=0;
            $_SESSION["exam_end"] = false;
            
            // Get the niveau and language selected
            $niveau = $_POST["niveau"];
            $idlangage = $_POST["language_id"];

            // Get the idabonne
            $idabonne = $_SESSION["idabonne"] ;

            // Get the max niveau
            // request the row that have the max niveau and a note equal to 4 or 5
            $niveau_per_language_sql = "SELECT MAX(niveau) FROM test WHERE idabonne = '$idabonne' and idlangage='$idlangage' and (note=4 or note=5)" ;
            $result_niveau = $conn->query($niveau_per_language_sql); 
            $row_niveau = mysqli_fetch_array($result_niveau);
            // if we found this row then the niveau in this row is the max_niveau +1
            $le_niveau = (int)$row_niveau[0] +1;
            if (empty($le_niveau)){$le_niveau=1;}
            $max_niveau=$le_niveau;

            //Check if the abonne can make the exam
            if(!($niveau > $max_niveau)){//acepted
                
                //Question Generation
                
                //Get the id of all question and stock it on array 
                $all_possible_question_id = array();
                $questions_sql = "SELECT noquestion FROM question where idlangage = '$idlangage' and niveau = '$niveau' ";
                $result_questions = $conn->query($questions_sql);
                if ($result_questions->num_rows > 0) {
                    while($row_questions = $result_questions->fetch_assoc()) {
                        $question_id=$row_questions["noquestion"];
                        array_push($all_possible_question_id,$question_id);
                    }
                }

                //  shuffle all the question and pick the first five
                shuffle($all_possible_question_id);
                $the_5_question=array();
                for($i=0;$i<5;$i++){
                    $the_5_question[$i]=$all_possible_question_id[$i];   
                }

                // Stock some variable 
                $_SESSION["the_5_question"]=$the_5_question;
                $_SESSION["question_number"]=1;
                $_SESSION["list_des_reponse"]=array();
                $_SESSION["test_niveau"]=$niveau;
                $_SESSION["test_langage"]=$idlangage;
                $_SESSION["exam_end"]=false;

                // initial question
                $the_question_id_now = $_SESSION["the_5_question"][0];
            }else{//not accepted
                header("Location: abonnee.php");//redirect
            }
        }
        else{// The exam has begin
            if($_SESSION["question_number"] == 5){// In case of the end of exam
                $_SESSION["exam_end"] = true;
                //get the last answer
                $le_choi = $_POST['le_choi'];
                array_push($_SESSION["list_des_reponse"],$le_choi);  
                // Get the number of correct answer
                $nombre_des_reponse_correct=0;
                for($i=0;$i<5;$i++){
                    $instant_question_id =$_SESSION["the_5_question"][$i];
                    $instant_reponse_id = $_SESSION["list_des_reponse"][$i];
                    
                    // Get the correct reponse for the instant question
                    $reponse_correct_sql = "SELECT noreponse FROM reponse Where correct=1 and noquestion = '$instant_question_id'";
                    $result_correct_reponse_id = $conn->query($reponse_correct_sql);                    
                    while($row_reponse_id = $result_correct_reponse_id->fetch_assoc()) {
                        $correct_reponse_id=$row_reponse_id["noreponse"];
                    }     
        
                    if((int)$correct_reponse_id === (int)$instant_reponse_id){$nombre_des_reponse_correct =$nombre_des_reponse_correct +1;}
                }
                $_SESSION["last_test_result"]=array($nombre_des_reponse_correct,date('Y/m/d'),$_SESSION['test_langage'],$_SESSION['test_niveau'],$_SESSION['idabonne']);
                // nombre des reponse correct , date , langage , niveau ,id abonne
                // Insert the test data
                $test_data_sql = "INSERT INTO test (note, datetest, idlangage,niveau,idabonne)
                VALUES ('$nombre_des_reponse_correct',now(),{$_SESSION['test_langage']},{$_SESSION['test_niveau']},{$_SESSION['idabonne']})";  // i removed the dots here !!!!
                if(!($conn->query($test_data_sql) === TRUE)) {
                    echo "Error: " . $test_data_sql . "<br>" . $conn->error;
                    
                }
                // reset all the variable
                $_SESSION["the_5_question"]=0;
                $_SESSION["question_number"]=1;
                $_SESSION["list_des_reponse"]=0;
                $_SESSION["test_niveau"]=0;
                $_SESSION["test_langage"]=0;
                
            }
            else{// in the exam
                $le_choi = $_POST['le_choi'];
                array_push($_SESSION["list_des_reponse"],$le_choi);             
                $the_question_id_now = $_SESSION["the_5_question"][$_SESSION["question_number"]];
                $_SESSION["question_number"] ++ ;
            }
            

        }
    }
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
    <div style='width:100%;border-radius:0;' class="badge bg-primary text-center" >
        <h2 class='text-center' id="head_bar">Question nombre <?php echo $_SESSION["question_number"]; ?> </h2>
    </div>
    <form id='question_form' class="border border-info rounded my-5 mx-auto p-3" style='width:700px;' action="test.php" method="post">
    <h4 class="text-start">La Question :</h4>
    <?php 
        
        $question_enonce_sql = "SELECT enonce FROM question WHERE noquestion='$the_question_id_now'";
        $result_question_enonce = $conn->query($question_enonce_sql);
                if ($result_question_enonce->num_rows > 0) {
                    while($row_questions_enonce = $result_question_enonce->fetch_assoc()) {
                        $enonce=$row_questions_enonce["enonce"];
                    }
                }
        echo $enonce;

    ?>
    <hr class='mx-3'>
    <h4 class="text-start">Les Reponse :</h4>
    <?php 
        //Get reponses data
        $get_reponse_fields_sql = "SELECT * FROM reponse WHERE noquestion  ='$the_question_id_now'"; 
        $result_of_reponse_fields = $conn->query($get_reponse_fields_sql);
        if ($result_of_reponse_fields->num_rows > 0) {
            $reponse_data=array();
            $reponse_number = 0;
            while($row_of_reponse_fields = $result_of_reponse_fields->fetch_assoc()) {
                $reponce_enonce=$row_of_reponse_fields["texte"];
                $no_reponse = $row_of_reponse_fields["noreponse"];
                array_push($reponse_data,array($no_reponse,$reponce_enonce));
                $reponse_number = $reponse_number +1;  
                echo $reponse_number.'  .  '. $reponce_enonce.'<br>';

            }
            }
           
    ?>
    <hr class='mx-3'>
    <h4 class="text-start">Votre Choix :</h4>
    <select class="form-select" aria-label="Default select example" name='le_choi'>
        <?php 
            for($i=1; $i<$reponse_number+1 ; $i++){
                echo '<option value="'.$reponse_data[$i-1][0].' "> '. $i.'</option>';
            }
        ?>

    </select>
    <br>
    <button type="submit" class="btn btn-outline-primary" style="width:100%;">Submit</button>
    <?php 
        if( $_SESSION["exam_end"]){
            $note=$_SESSION["last_test_result"][0];
            $date=$_SESSION["last_test_result"][1];
            $langage=$_SESSION["last_test_result"][2];
            $niveau=$_SESSION["last_test_result"][3];
            $idabonne=$_SESSION["last_test_result"][4];
            
            include('connection.php');  

            $langage_sql = "SELECT  nomlangage FROM langage WHERE idlangage = '$langage'";
            $result_langage = $conn->query($langage_sql);

            if ($result_langage->num_rows > 0) {
                while($row_langage = $result_langage->fetch_assoc()) {        
                    $langage = $row_langage['nomlangage'];
                }            
                }            
            $username = $_SESSION['username'];
            echo '
            <script>
            document.getElementById("head_bar").innerHTML="Résultat";
            document.getElementById("question_form").innerHTML="";
        </script>
        La langage : '.$langage .' <br>
        Le niveau : '.$niveau.' <br>
        Le Date : '.$date.'<br>
        La Note : '.$note.'<br>
        Le Username : '.$username.'<br>
        
            ';
        }
        $_SESSION["exam_end"] = false;
    ?>
    </form>
    <a href="http://127.0.0.1/Projet_web2/abonnee.php">Revenir</a>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
