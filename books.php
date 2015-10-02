<?php

/*********************************************************************
 * Library class.
 * 
 * Works with SQL. Connect to the database, insert, search and update
 * books in the library database.
 * 
 * Methods: insertBook, searchAuthor, searchTitle, searchGenre, 
 * setIdent and updateBook.
 ********************************************************************/


class Library {
    public static $username = "root";
    public static $password = "caviakooi";
    
    /**
     * Constructor function.
     * Make the connection to the database.
     */
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=tryout", self::$username, self::$password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    
    /**
     * Add a book to the library database.
     * @param string $author
     * @param string $title
     * @param string $genre
     */
    public function insertBook($author, $title, $genre) {
        try {
            // Call doubleCheck function to see whether the book is already in the db
            $inDB = $this->doubleCheck($author, $title);
            
            // If not, add the book to the db
            if (!$inDB) {
                $stmt = $this->conn->prepare("INSERT INTO books(author, title, genre) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $author);
                $stmt->bindParam(2, $title);
                $stmt->bindParam(3, $genre);
                $stmt->execute();

                echo "<br /><b>New book added successfully!</b><br /><br />";
                echo "<div id='added'><b>$author</b><br />$title<br /><i>$genre</i><br /></div>";
            } else {
                // If the book is already in the db:
                echo "<br /><b>This book is already present in the library!</b>";
            }
        }
        catch(PDOException $e) {
            echo $sql . "<br />" . $e->getMessage();
        }
    }
    
    /**
     * Search for author in the library database.
     * @param string $author
     */
    public function searchAuthor($author) {
        try {
            // escape quotes
            $checkAuthor = str_replace("'", "''", $author);
            // search the database, using wildcards
            $sql = "SELECT * FROM books WHERE author LIKE '%$checkAuthor%' ORDER BY title";
            $stmt = $this->conn->query($sql);
            
            //  fetch as numeric array
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            
            // show results in a list of update forms
            echo "<br /><div id='searchResult'><h3>Author: $author</h3><ul>";
            while ($row = $stmt->fetch()) {
                // call result form function
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Search for title in the library database.
     * @param string $title
     */
    public function searchTitle($title) {
        try {
            // escape quotes
            $checkTitle = str_replace("'", "''", $title);
            // search the database, using wildcards
            $sql = "SELECT * FROM books WHERE title LIKE '%$checkTitle%' ORDER BY author";
            $stmt = $this->conn->query($sql);
            
            // fetch results as numeric array
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            // show results in a list of update forms
            echo "<br /><div id='searchResult'><h3>Title: -$title-</h3><ul>";
            while ($row = $stmt->fetch()) {
                // call result form function
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Search for genre in the library database.
     * @param string $genre
     */
    public function searchGenre($genre) {
        try {
            // search the database
            $sql = "SELECT id, author, title FROM books WHERE genre = '$genre' ORDER BY author, title";
            $stmt = $this->conn->query($sql);
            
            // fetch results as numberic array
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            // show results in a list of update forms
            echo "<br /><div id='searchResult'><h3>Genre: $genre</h3><ul>";
            while ($row = $stmt->fetch()) {
                // call result form function
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Check whether the book the user wants to add is already in the database
     * @param string $author
     * @param string $title
     * @return boolean
     */
    public function doubleCheck($author, $title) {
        // search the db for author/title combination
        $check = $this->conn->prepare("SELECT COUNT(*) FROM books WHERE author = ? AND title = ?");
        $check->bindParam(1, $author);
        $check->bindParam(2, $title);
        $check->execute();
        
        // return true if a record is found, false if none is found
        if ($check->fetchColumn() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Take a row from the search result and put the data in an update form.
     * @param array $row
     */
    public function echoResultForm($row) {
        // a list item contains a div, which contains a form
        echo "<li><div id='result'>";
        echo "<form method='post' action='index.php'>";
        // hidden input with book id value
        echo "<input type='hidden' name='ident' value='$row[0]'/>";
        
        // with or without the genre
        if (count($row) == 3) {
            echo "<b>" . $row[1] . "</b><br />" . $row[2] . "<br />";
        } else {
            echo "<b>" . $row[1] . "</b><br />" . $row[2] . "<br /><i>" . $row[3] . "</i><br />";
        }
        
        // submit button
        echo "<input type='submit' name='updateBook' value='Update Book' />";
        echo "</form></div></li>";
    }
    
    /**
     * Set the id number of the book that the user wants to update as a property.
     * Use the id to search for the book in the db and set author, title and
     * genre as properties.
     * @param integer $ident
     */
    public function setIdent($ident) {
        // set id property
        $this->ident = $ident;
        
        // use id to search for the book in db
        $book = $this->conn->query("SELECT * FROM books WHERE id = $ident");
        $result = $book->fetch();
        
        // set author, title and genre as properties
        $this->author = $result[1];
        $this->title = $result[2];
        $this->genre = $result[3];
    }
    
    /**
     * Update a book in the library database.
     * @param integer $ident
     * @param string $author
     * @param string $title
     * @param string $genre
     */
    public function updateBook($ident, $author, $title, $genre) {
        try {
            // prepare update statement and bind the parameters
            $stmt = $this->conn->prepare("UPDATE books SET author = ?, title = ?, genre = ? WHERE id = ?");
            $stmt->bindParam(1, $author);
            $stmt->bindParam(2 , $title);
            $stmt->bindParam(3, $genre);
            $stmt->bindParam(4, $ident, PDO::PARAM_INT);
            
            // update record
            $stmt->execute();
            echo "<br /><b>Book updated successfully!</b>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
