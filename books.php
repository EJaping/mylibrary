<?php

class Library {
    public static $username = "root";
    public static $password = "caviakooi";
    
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=tryout", self::$username, self::$password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    
    public function insertBook($author, $title, $genre) {
        try {
            $checkAuthor = str_replace("'", "''", $author);
            $checkTitle = str_replace("'", "''", $title);
            
            $inDB = $this->doubleCheck($author, $title);
            
            if (!$inDB) {
                $stmt = $this->conn->prepare("INSERT INTO books(author, title, genre) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $author);
                $stmt->bindParam(2, $title);
                $stmt->bindParam(3, $genre);
                $stmt->execute();

                echo "<br /><b>New book added successfully!</b><br /><br />";
                echo "<div id='added'><b>$author</b><br />$title<br /><i>$genre</i><br /></div>";
            } else {
                echo "<br /><b>This book is already present in the library!</b>";
            }
        }
        catch(PDOException $e) {
            echo $sql . "<br />" . $e->getMessage();
        }
    }
    
    public function searchAuthor($author) {
        try {
            $checkAuthor = str_replace("'", "''", $author);
            $sql = "SELECT * FROM books WHERE author LIKE '%$checkAuthor%' ORDER BY title";
            $stmt = $this->conn->query($sql);
            
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            echo "<br /><div id='searchResult'><h3>Author: $author</h3><ul>";
            while ($row = $stmt->fetch()) {
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function searchTitle($title) {
        try {
            $checkTitle = str_replace("'", "''", $title);
            $sql = "SELECT * FROM books WHERE title LIKE '%$checkTitle%' ORDER BY author";
            $stmt = $this->conn->query($sql);
            
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            echo "<br /><div id='searchResult'><h3>Title: -$title-</h3><ul>";
            while ($row = $stmt->fetch()) {
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function searchGenre($genre) {
        try {
            $sql = "SELECT id, author, title FROM books WHERE genre = '$genre' ORDER BY author, title";
            $stmt = $this->conn->query($sql);
            
            $result = $stmt->setFetchMode(PDO::FETCH_NUM);
            echo "<br /><div id='searchResult'><h3>Genre: $genre</h3><ul>";
            while ($row = $stmt->fetch()) {
                $this->echoResultForm($row);
            }
            echo "</ul></div>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function doubleCheck($author, $title) {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM books WHERE author = ? AND title = ?");
        $check->bindParam(1, $author);
        $check->bindParam(2, $title);
        $check->execute();
        if ($check->fetchColumn() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function echoResultForm($row) {
        echo "<li><div id='result'>";
        echo "<form method='post' action='index.php'>";
        echo "<input type='hidden' name='ident' value='$row[0]'/>";
        if (count($row) == 3) {
            echo "<b>" . $row[1] . "</b><br />" . $row[2] . "<br />";
        } else {
            echo "<b>" . $row[1] . "</b><br />" . $row[2] . "<br /><i>" . $row[3] . "</i><br />";
        }
        echo "<input type='submit' name='updateBook' value='Update Book' />";
        echo "</form></div></li>";
    }
    
    public function setIdent($ident) {
        $this->ident = $ident;
        $book = $this->conn->query("SELECT * FROM books WHERE id = $ident");
        
        $result = $book->fetch();
        $this->author = $result[1];
        $this->title = $result[2];
        $this->genre = $result[3];
    }
    
    public function updateBook($ident, $author, $title, $genre) {
        try {
            $stmt = $this->conn->prepare("UPDATE books SET author = ?, title = ?, genre = ? WHERE id = ?");
            $stmt->bindParam(1, $author);
            $stmt->bindParam(2 , $title);
            $stmt->bindParam(3, $genre);
            $stmt->bindParam(4, $ident, PDO::PARAM_INT);
            
            $stmt->execute();
            echo "<br /><b>Book updated successfully!</b>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
