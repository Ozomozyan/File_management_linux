<?php

$target_dir = "/var/www/html/myproject/uploads/"; // Define this at the start

// Delete file if delete request is made
if(isset($_GET['delete'])){
    $file = $target_dir . basename($_GET['delete']);
    if(file_exists($file)){
        unlink($file);
        echo "File deleted.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "/var/www/html/myproject/uploads/"; // Directory where files will be saved
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (example: limit to 5MB)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Attempt to upload file
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}


// Edit file if edit request is made
if(isset($_GET['edit'])){
    $editFile = $target_dir . basename($_GET['edit']);
    if(file_exists($editFile)){
        $editableText = file_get_contents($editFile);
    } else {
        echo "File not found for editing: " . htmlspecialchars($editFile);
    }
}

// Save edited file
if(isset($_POST['save']) && isset($_POST['editedText']) && isset($_POST['editingFile'])){
    $fileToEdit = $target_dir . basename($_POST['editingFile']);
    file_put_contents($fileToEdit, $_POST['editedText']);
    echo "File saved.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #dashboard {
            padding: 40px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">File Manager</a>
        <!-- Other navigation items here -->
    </nav>

    <!-- File Manager Dashboard -->
    <section class="container mt-5" id="dashboard">
        <div class="row">
            <div class="col-md-12">
                <h2>File Upload</h2>
                <form action="index.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fileToUpload">Select file to upload:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload">
                    </div>
                    <input type="submit" value="Upload File" name="submit" class="btn btn-primary">
                </form>

                <!-- Dynamic File Listing -->
                <h2>Uploaded Files</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // List files in the directory
                        if (is_dir($target_dir)){
                            if ($dh = opendir($target_dir)){
                                while (($file = readdir($dh)) !== false){
                                    if ($file != "." && $file != "..") {
                                        echo "<tr><td>".$file."</td>";
                                        echo "<td><a href='?edit=".$file."' class='btn btn-warning btn-sm'>Edit</a> ";
                                        echo "<a href='?delete=".$file."' class='btn btn-danger btn-sm'>Delete</a></td></tr>";
                                    }
                                }
                                closedir($dh);
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Editing Section -->
                <?php if(isset($editableText) && isset($editFile)): ?>
                    <h2>Editing File: <?php echo htmlspecialchars(basename($editFile)); ?></h2>
                    <form action="index.php" method="post">
                        <textarea name="editedText" rows="10" cols="50" class="form-control"><?php echo htmlspecialchars($editableText); ?></textarea>
                        <input type="hidden" name="editingFile" value="<?php echo htmlspecialchars(basename($editFile)); ?>">
                        <input type="submit" name="save" value="Save Changes" class="btn btn-success mt-2">
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
