<?php
  // Database connection
  $dbh = new PDO('sqlite:upload.db');
  $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Create folders if they don't exist

  if (!is_dir('images')) mkdir('images');
  if (!is_dir('images/originals')) mkdir('images/originals');
  if (!is_dir('images/thumbs_small')) mkdir('images/thumbs_small');
  if (!is_dir('images/thumbs_medium')) mkdir('images/thumbs_medium');

  // PHP saves the file temporarily here
  // Image is the name of the file input in the form
  $tempFileName = $_FILES['image']['tmp_name'];

  // Create an image representation of the original image
  // @ before function is to prevent warning messages
  $original = @imagecreatefromjpeg($tempFileName);
  if (!$original) $original = @imagecreatefrompng($tempFileName);
  if (!$original) $original = @imagecreatefromgif($tempFileName);

  if (!$original) die('Unknown image format!');

  // Insert image data into database
  $stmt = $dbh->prepare("INSERT INTO images VALUES(NULL, ?)");
  $stmt->execute(array($_POST['title']));

  // Get image ID
  $id = $dbh->lastInsertId();

  // Generate filenames for original, small and medium files
  $originalFileName = "images/originals/$id.jpg";
  $smallFileName = "images/thumbs_small/$id.jpg";
  $mediumFileName = "images/thumbs_medium/$id.jpg";

  $width = imagesx($original);     // width of the original image
  $height = imagesy($original);    // height of the original image
  $square = min($width, $height);  // size length of the maximum square

  // Save original file as jpeg (even if it came in a different format)
  imagejpeg($original, $originalFileName);

  // We could also copy the file directly without converting to jpeg
  // move_uploaded_file($_FILES['image']['tmp_name'], $originalFileName);

  // Create and save a small square thumbnail
  $small = imagecreatetruecolor(200, 200);
  imagecopyresized($small, $original, 0, 0, ($width>$square)?($width-$square)/2:0, ($height>$square)?($height-$square)/2:0, 200, 200, $square, $square);
  imagejpeg($small, $smallFileName);

  // Calculate width and height of medium sized image (max width: 400)
  $mediumwidth = $width;
  $mediumheight = $height;
  if ($mediumwidth > 400) {
    $mediumwidth = 400;
    $mediumheight = $mediumheight * ( $mediumwidth / $width );
  }

  // Create and save a medium image
  $medium = imagecreatetruecolor($mediumwidth, $mediumheight);
  imagecopyresized($medium, $original, 0, 0, 0, 0, $mediumwidth, $mediumheight, $width, $height);
  imagejpeg($medium, $mediumFileName);

  header("Location: index.php");
?>
