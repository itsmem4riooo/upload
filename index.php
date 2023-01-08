<?php

require 'vendor/autoload.php';
use Sources\Classes\Helpers\Upload;

$Error = null; $Success = null;
$ImageExemple = new Upload('upload_exemple');

if(!empty($_FILES['image_test'])){

  $ImageExemple->image($_FILES['image_test'],300,['image/png','image/jpeg','image/gif','image/webp']);

  if($ImageExemple->getError()){
    $Error = $ImageExemple->getError();
  }else{
    $Success = $ImageExemple->getFile()['name'];
  }

}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Upload php exemple</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body>
        <div class="container col-md-6 p-2">
            <?php if(!empty($Error)){ ?>
                <div class="alert alert-danger"><?php echo $Error?></div>
            <?php } ?>
            <?php if(!empty($Success)){ ?>
                <div class="alert alert-success">Image uploaded successffuly ! <a href="<?php echo $Success?>"><?php echo $Success?></a></div>
            <?php } ?>
            <div class="mb-5">
                <form method="post" action="" enctype="multipart/form-data">
                <label for="Image" class="form-label">Upload php exemple</label>
                <input class="form-control" type="file" name="image_test" id="formFile" onchange="preview()">
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
            <img id="frame" src="" class="img-fluid" />
        </div>

        <script>
            function preview() {
                frame.src = URL.createObjectURL(event.target.files[0]);
            }
        </script>
    </body>
</html>  