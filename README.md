# QCM

!!! DONT FORGET TO CREATE THE DATABASE !!!!

Les Page dans la Site :
    1. Authentication Page
    2. Admin home
    3. Create a question
    4. Modifie a question
    5. Abonne home
    6. Test Page

    page without interface (log_out.php ,connection.php)


1. Authentication page :
    
    look :
        The authentication page is the start point of this website,
        the type of the user that you sign in with lead you to your job ,
        if you sign in with a admin user you will go to the adminhome page ,
        if you sign in with a abonne user you will go the abonne page.
        To SIGN IN with a user you have to enter :
            1. the type of your user account
            2. the username of your user account
            3. the password of your user account
        If those input match then you will sign in .
    Some point behind the ligne :
        The authentication_page.php page its a page with a form with an action to the same page and POST method
        the input in this form are (user_type,username,password)
        After submiting the form :
            1. Check if its a POST request
            2. Check if the parameter of this request are right
            3. Check for empty field
        after this check there are two way to sign in based on the type of the user .
        in general :
            we request a row from the database , from the table (admin or abonne) with a condition (password and username) ,
            if we get one row then there is account match with the input , then the authentication success
            after the successfull sign in the data of the user are saved in the session (username,password,usertype,nom,prenom,id)
            after that , redirection 

2. Admin home :

    look :
        adminhome page is the place that you can read all the question , and access to modifie them , delete them , and create one
        you can also filter the question by language

    Some point behind the ligne :
        First we check if the user is admin else we will redirect him to the authentication page
        in this page we request from the database from the question table every object 
        we fill them on a table (question , niveau ,language,faite par)
        and we add two colum for (modification , delete)
        modification is a get form with action to modifie_question.php with question id as a parametre
        delete is a post form with action to adminhome.php with the id of the question as parametre
        the filter is a post form with action to adminhome.php with the id of the language as a parametre
        the filter work on add a condition when we request the data from the table question (this condition is the language selected )

3. Create a question :

    look :
        You can create a question by selecting a language for the question , niveau for the question  , the enonce of the question , and you can add 3 to 5 response
        then you fill the response , you choose the correct reponse
        make sure that you fill all the field and put a question mark for the question enonce 

    Some point behind the ligne :
        First we check if the user is admin else we will redirect him to the authentication page
        we request the available language to create the question 
        the niveau is between 1 and 3 
        you choose the number of reponse 3 , 4 ,5 
        after clicking one of those button a javascript code run and genereate the number of the requested question and show the submit button for you

        after filling the form and submiting :
        1.Check if its a post request
        2. Check for the parametere of the request
        3. Check if there is one of those (input) are empty 

        after checking we stock the input data in variables 
        insert the data to the database

        if there is an error with the processuss the selected language and niveau remain , and a message will be displayed

        after submit successfully ther is no redirection and the  selected language and niveau remain .

4. Modifie a question page :
    look:
        when you open the page , the form of the page is like the 'create a question ' page but in this page there are already the initial data
        ! if you change the number of reponse all the initial reponse will be clear
        you cant modifie the question if you are not the one who made the question
    Some point behind the ligne :
        First we check if the user is admin else we will redirect him to the authentication page
        we request the available language to create the question 
        note that to get to this page there are some parametre must be sendet with your request containing the id of the question 
        there are two way by get request (when you are at the admin home and click modifie , a get request with the id of the question as parametre)
        the other way is by post request (after submiting the modification form the the id of the question will be sendet to display the new data)
        the processuss is like create question ,  but here we chekc if the user who made the question is the same that who want to modifie it


        THE PAGE IS FLEXIBLE WITH THE NUMBER OF language

5. Abonne Home :
    look :
        This page is landing for the abonne after sign in 
        in this page you choose the language and the level of the test that you want to do 
        you can see the record of your account (your level in the available language )
        you can see all the history of your tests 
    Some point behind the ligne :
        First we check if the user is with an abonne type user 
        we request the available language to create the question 
        we Stock the language available in array and stock the level of every language for the user in array 
        to find the level in each language we request the test related with the user we get the row that have the max level and a note equal to 4 or 5 
        then if we find this row we add one to the level of the row collected 
        the form in this page is a post form with action to test.php page , the input of this form is the niveau and the language
        we displayy all the history by requesting all the test related to the user and we display the result in a table
        we display the level for each language to the user 

6. Test Page
    look :
        you access properly to this page with two method only , a request from the abonne home with language and niveau as parametre
        or by submiting (answering the question ).
        you enter the page facing the first question 
        you Select an answear 
        you submit
        you move to the next question 
        and so ....
        till the final question (question number 5)
        you will know the note of the test 
        the data will be inseret to the database 
        you can return to the home
    Some point behind the ligne :
        First we check if the user type is right
        initial setup of the test :
            reset some variable in the session
            get the niveau and the language of the test 
            Check for the max niveau 
            Genereate the question 
                collect all the question with this language and this niveau
                Shufflee all those question
                pick the first 5
            we save the inital setup variable in the session
        in the exam
            recive the past form 
            add the answer to the list of answer 
            set the new question
        in the end of the exam
            get the last answer
            stock it on the list
            compare the list with the list of the right reponse
            get the total of correct answer
            insert the data of the test in the database(note,date,niveau,langage)

Data Base structure :
    Abonne :
        idabonne
        nom
        prenom
        username
        password
    Admin :
        idadmin
        nom
        prenom
        username
        password
    Langage
        idlangage
        nomlangage
    question
        noquestion
        enonce
        niveau
        idlangage
        idadmin
    reponse
        noreponse
        texte
        correct
        noquestion
    test 
        notest
        note
        datetest
        idlangage
        niveau
        idabonne

Sory for my bad english but i am also bad in french
the code should be more flexible sorry for that (the number of question for the exam , the level of language , the mehode of set the level for a user for a language)
those point take some time (i dont have it :)  )
to mention the code is flexible with the language
the structure of the data base is fine 

personal email : mhd2002mswi@gmail.com
email : omencodes@gmail.com
the site : omencodes.com (maybe broken now bcs of the lebanese problem)
github : https://github.com/omencodes/QCM



