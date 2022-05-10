<?php
  $dbh = new PDO('sqlite:upload.db');
  $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $dbh->prepare("SELECT * FROM images ORDER BY id DESC");
  $stmt->execute();
  
  $images = $stmt->fetchAll();
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Upload Example</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css"> 
  </head>
  <body>
    <header>
      <h1><a href="index.php">Images</a></h1>
    </header>
    <nav>
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <label>Title:
          <input type="text" name="title">
        </label>
        <input type="file" name="image">
        <input type="submit" value="Upload">
      </form>
    </nav>
    <section id="images">
      <?php foreach ($images as $image) { ?>
      <article class="image">
        <header><h2><?=$image['title']?></h2></header>
        <a href="view_image.php?id=<?=$image['id']?>">
          <img src="images/thumbs_small/<?=$image['id']?>.jpg" width="200" height="200">
        </a>
      </article>
      <?php } ?>
    </section>
  </body>
</html>
