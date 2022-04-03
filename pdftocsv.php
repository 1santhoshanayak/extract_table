<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

/**
 * @file
 * Thank you Zeshan Ziya and Amit Shriwardhankar
 */
$tmp_file_path = "/tmp";
$file_key = strip_tags($_REQUEST['file_key']);
if ($_REQUEST['download'] == '1') {
  $file_path = $tmp_file_path . '/' . $file_key . '.csv';
  $dest_file_path = $tmp_file_path . '/' . $file_key . '.pdf';
  header('Content-Disposition: attachment; filename=' . basename($file_path));
  header("Content-type:text/csv");
  header('Content-Length: ' . filesize($file_path));
  header('Content-Transfer-Encoding: binary');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  readfile($file_path);
  unlink($file_path);
  unlink($dest_file_path);
  exit;
}

if (!empty($file_key)) {
  $file_path = $tmp_file_path . '/' . $file_key . '.csv';
  if (file_exists($file_path)) {
    echo "Table conversion Completed. <a href='pdftocsv.php?download=1&file_key=".$file_key."'>Click here</a> to download";
    echo '<script>
      alert("Table Conversion completed");
    </script>';
  }
  else {
    echo "Table conversion in progress. It will automatically download once completed. Please wait..";
    echo '<script>
      setInterval(function() {
        location.href = location.href;
      }, 5000);
    </script>';
  }
  return;
}

if (!empty($_REQUEST['convert_file']) && $_REQUEST['convert_file'] == "Submit") {
  if (empty($_FILES)) {
    echo "FIle Not Found.";
    exit;
  }

  if (!empty($_FILES)) {
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ["pdf"])) {
      echo "Please Upload pdf files only";
      exit;
    }
  }
  if (!empty($_FILES)) {

    $file_name     = $_FILES['file']['name'];
    $file_path     = $_FILES['file']['tmp_name'];
    $pages         = $_REQUEST['pages'];
    if ($pages == '') {
      $pages = 'all';
    }
    if (file_exists($file_path)) {
      $key = date('YmdHis') . "_" . md5($_FILES['file']['name']);
      $file_name = $key . '.pdf';
      $src_file_path = $tmp_file_path . '/' . $file_name;
      $dest_file_path = $tmp_file_path . '/' . $key . '.csv';
      if (move_uploaded_file($_FILES['file']['tmp_name'], $src_file_path)) {
        $cmd = "nohup python3.9 /var/www/html/master.py '{$src_file_path}' '{$pages}' '{$dest_file_path}' > /dev/null &";
        exec($cmd); 
        header('location:pdftocsv.php?file_key='.$key);
      }
    }
  }
}


echo '
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" style="margin-left: 26px;"  href="#">PDF to CSV</a>
    </nav>
<div class="container">

<div class="row">
<br><br><br><br>


<form enctype="multipart/form-data" action="pdftocsv.php" method="post"  accept-charset="UTF-8">


<div>
  <div class="form-type-file form-item-intake-file form-item form-group">
   


<br><br><br><br>
<br><br>
 <div class="col-md-3">
      <label for="intake_file">Choose PDF Files Only </label>
      <input style="padding-bottom: 32px !important; width: 37rem" class="form-control form-file" type="file" id="file" name="file">
    </div>
    <div class="col-md-3">
      <label for="pages">Page No </label>
      <input style="width: 37rem;" class="form-control form-file" type="text" name="pages" id="pages">
    </div>
    <div class="col-md-12">
      <input type="submit" class="btn btn-submit btn-primary pull-left" id="convert_file" name="convert_file" value="Submit" style="margin-top:10px;">
    </div>
  </div>
</div>
</form>


</div>

</div>

';

