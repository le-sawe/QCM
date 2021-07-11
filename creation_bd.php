<?php
		$servername = "localhost";
		$username = "root";
		$password = "";
		
		// Create connection
		$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {						
	$link = mysqli_connect('localhost', 'root', '') or 
		die(mysqli_connect_errno()." : ".mysqli_connect_error());
		mysqli_query($link, "create database web_quiz") 
			or die(" Erreur de creation de base de donnees");
		mysqli_select_db($link, 'web_quiz')or die("erreur de selection BD");
	
		$req = "create table abonne(idabonne int primary key, ";
		$req .= " nom varchar(15) not null, prenom varchar(15) not null, "; 
		$req .= " username varchar(8) not null, password varchar(8) not null)";
		mysqli_query($link, $req);
		
		$req = "create table admin(idadmin int primary key, ";
		$req .= " nom varchar(15) not null, prenom varchar(15) not null, "; 
		$req .= " username varchar(8) not null, password varchar(8) not null)";
		mysqli_query($link, $req);
		
		$req = "create table langage(idlangage int primary key, ";
		$req .= " nomlangage varchar(15) not null)"; 
		mysqli_query($link, $req);
		
		$req = "create table question(noquestion int auto_increment primary key, ";
		$req .= " enonce varchar(200) not null, niveau tinyint(1) not null, ";
		$req .= " idlangage int not null references langage (idlangage), "; 
		$req .= " idadmin int not null references admin(idadmin))";
		mysqli_query($link, $req);

		$req = "create table reponse(noreponse int auto_increment primary key, ";
		$req .= " texte varchar(200) not null, correct boolean not null, ";
		$req .= " noquestion int not null references question (idquestion)) "; 
		mysqli_query($link, $req);


		$req = "create table test(notest int auto_increment primary key, ";
		$req .= " note tinyint(1) not null, datetest date not null, ";
		$req .= " idlangage int not null references langage (idlangage), "; 
		$req .= " niveau tinyint(1) not null, idabonne int not null references abonne(idabonne))";
		mysqli_query($link, $req);

		// Langage
		mysqli_query ($link, "insert into langage values(1, 'HTML')");
		mysqli_query ($link, "insert into langage values(2, 'JavaScript')");
		mysqli_query ($link, "insert into langage values(3, 'PHP')");
		
		// Admin
		mysqli_query ($link, "insert into admin values(1, 'Karim', 'Karam', 'admin1', 'admin1')");
		mysqli_query ($link, "insert into admin values(2, 'Salim', 'Salem', 'admin2', 'admin2')");

		// Abonne
		mysqli_query ($link, "insert into abonne values(1, 'Rim', 'Karam', 'abonne1', 'abonne1')");
		mysqli_query ($link, "insert into abonne values(2, 'Sami', 'Sam', 'abonne2', 'abonne2')");
		mysqli_query ($link, "insert into abonne values(3, 'Joe', 'Fadi', 'abonne3', 'abonne3')");
		mysqli_query ($link, "insert into abonne values(4, 'Kamil', 'Kim', 'abonne4', 'abonne4')");
	
		// Some initial question and response to test the program directly

		// Question noquestion enonce niveau idlangage idadmin
		mysqli_query ($link, "insert into question values(1, 'JavaScript is a ___ -side programming language.', 1,2, 1)");
		mysqli_query ($link, "insert into question values(2, 'Which of the following will write the message Hello DataFlair! in an alert box?', 1,2, 1)");
		mysqli_query ($link, "insert into question values(3, 'How do you find the minimum of x and y using JavaScript?', 1,2, 1)");
		mysqli_query ($link, "insert into question values(4, 'If the value of x is 40 then what is the output of the following program?(x % 10 == 0)? console.log(Divisible by 10) : console.log(Not divisible by 10);', 1,2, 1)");
		mysqli_query ($link, "insert into question values(5, 'Which JavaScript label catches all the values, except for the ones specified?', 1,2, 1)");
		
		// Reponse noreponse texte correct noquestion
			// for question 1
		mysqli_query ($link, "insert into reponse values(1, 'Client', 0,1)");
		mysqli_query ($link, "insert into reponse values(2, 'Server', 0,1)");
		mysqli_query ($link, "insert into reponse values(3, 'Both', 1,1)");
		mysqli_query ($link, "insert into reponse values(4, 'None', 0,1)");
			// for question 2
		mysqli_query ($link, "insert into reponse values(5, 'alertBox(Hello DataFlair!);', 0,2)");
		mysqli_query ($link, "insert into reponse values(6, ' alert(Hello DataFlair!);', 0,2)");
		mysqli_query ($link, "insert into reponse values(7, ' msgAlert(Hello DataFlair!);', 0,2)");
		mysqli_query ($link, "insert into reponse values(8, 'alert(Hello DataFlair!);', 1,2)");
			// for question 3
		mysqli_query ($link, "insert into reponse values(9, 'min(x,y);', 0,3)");
		mysqli_query ($link, "insert into reponse values(10, '  Math.min(x,y)', 1,3)");
		mysqli_query ($link, "insert into reponse values(11, ' Math.min(xy)', 0,3)");
		mysqli_query ($link, "insert into reponse values(12, ' min(xy);', 0,3)");
			// for question 4
		mysqli_query ($link, "insert into reponse values(13, 'ReferenceError', 0,4)");
		mysqli_query ($link, "insert into reponse values(14, '  Divisible by 10', 1,4)");
		mysqli_query ($link, "insert into reponse values(15, '  Not divisible by 10', 0,4)");
		mysqli_query ($link, "insert into reponse values(16, ' None of the above', 0,4)");
			// for question 5
		mysqli_query ($link, "insert into reponse values(17, 'catch', 0,5)");
		mysqli_query ($link, "insert into reponse values(18, '  label', 0,5)");
		mysqli_query ($link, "insert into reponse values(19, '   try', 0,5)");
		mysqli_query ($link, "insert into reponse values(120, ' default', 1,5)");
		
		
		echo "bd cree";
		mysqli_close($link);	  
		header("Location: authentication_page.php");
	}
	echo "The DATABASE Already Created !";
?>