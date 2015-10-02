<!DOCTYPE html>

<h3>Update a book record!<br /><sub>Change the author, title and genre of the book.</sub></h3>
<form method="post" action="index.php">
    <input type='hidden' name='ident' value="<?php echo $library->ident; ?>"/>
    <p id="updateForm">
        Author:<br />
        <input type="text" name="author" value="<?php echo $library->author; ?>" />
    </p>
    <p id="updateForm">
        Title:<br />
        <input type="text" name="title" value="<?php echo $library->title; ?>" />
    </p>
    <p id="updateForm">
        Genre:<br />
        <input type="text" name="genre" value="<?php echo $library->genre; ?>" />
    </p>
    <p>
        <input type="submit" name="update" value="Update" />
    </p>
</form>
