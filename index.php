<!DOCTYPE html>
<!--
This is a library! Add books, search for books on author, title or genre, or update records.

To do: make a second table in the database with loans: book id, and who has the book.
Add button to searchresults to borrow the book, and add borrowed or not to results.
Make a display of books that are borrowed and by whom.
-->
<?php
    include "books.php";
    
    // make new library object
    $library = new Library();
    
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="default.css"  />
        <title>My Library</title>
    </head>
    <body>
        
        <h1><a href="index.php">My Library</a></h1>
        
        <div id="main">
            <?php include "main.html"; ?>
        </div>
            
        <div id="secondary">
            <?php
                // handle form submissions
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    
                    if (isset($_POST["add"])) {
                        // user wants to add a book to the database: show add form
                        include "add.html";
                        
                    } elseif (isset($_POST["search"])) {
                        // user wants to search the database: show search form
                        include "search.html";
                        
                    } elseif (isset($_POST["addBook"])) {
                        // add a book to the database
                        
                        if (empty($_POST["author"]) || empty($_POST["title"]) || empty($_POST["genre"])) {
                            // check if all fields are used, if not, tell user
                            echo "<br /><b>You need to fill in all the fields!</b>";
                        } else {
                            // set variables
                            $author = $_POST["author"];
                            $title = $_POST["title"];
                            $genre = $_POST["genre"];
                            
                            // insert book into database
                            $library->insertBook($author, $title, $genre);
                        }                        
                    } elseif (isset($_POST["startAuthorSearch"])) {
                        // search on author
                        $author = $_POST["authorSearch"];
                        $library->searchAuthor($author);
                            
                    } elseif (isset($_POST["startTitleSearch"])) {
                        // search on title
                        $title = $_POST["titleSearch"];
                        $library->searchTitle($title);

                    } elseif (isset($_POST["startGenreSearch"])) {
                        // search on genre
                        $genre = $_POST["genreSearch"];
                        $library->searchGenre($genre);

                    } elseif (isset($_POST["updateBook"])) {
                        // user wants to update a book: show update form
                        $ident = $_POST["ident"];
                        $library->setIdent($ident);
                        include "update.php";
                        
                    } elseif (isset($_POST["update"])) {
                        // update the book
                        $ident = $_POST["ident"];
                        $author = $_POST["author"];
                        $title = $_POST["title"];
                        $genre = $_POST["genre"];
                        $library->updateBook($ident, $author, $title, $genre);
                    }
                }
            ?>
        </div>
        
        <div id="images">
            <img src="images/fantasylibrary3.jpg" alt="Library"/>
        </div>
                    
    </body>
</html>
