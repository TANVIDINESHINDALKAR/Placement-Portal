<?php
// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

if (isset($_POST['submit'])) {
    // Get form input values
    $subject = $_POST['subject'];
    $notice = $_POST['input'];
    $audience = $_POST['audience'];

    // Check if file is uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        // Folder where you want to save your resume
        $folder_dir = "../uploads/resume/";

        // Getting Basename of file
        $base = basename($_FILES['resume']['name']);

        // Get the file extension
        $resumeFileType = pathinfo($base, PATHINFO_EXTENSION);

        // Setting a random non-repeatable file name
        $file = uniqid() . "." . $resumeFileType;

        // Path to save the file
        $filename = $folder_dir . $file;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $filename)) {
            // File uploaded successfully
        } else {
            // Error uploading file
            // Handle the error appropriately
            $file = ""; // Set $file to empty string or default value
        }
    } else {
        // No file uploaded or error occurred
        // Handle the case where no file is uploaded or error occurred
        $file = ""; // Set $file to empty string or default value
    }

    // Generate a unique hash
    $hash = md5(uniqid());

    // SQL query to insert notice into database
    $sql = "INSERT INTO notice(subject, notice, audience, resume, hash, `date`) VALUES ('$subject', '$notice', '$audience', '$file', '$hash', NOW())";

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        // Insertion successful
        include 'sendmail.php';
        header("Location: postnotice.php");
        exit();
    } else {
        // Error executing SQL query
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Placement Portal</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/_all-skins.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="css/custom.css">


    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-green sidebar-mini">
    <?php

    include 'header.php';
    ?>



    <div class="row">
        <div class="col-xs-6 responsive">
            <section>
                <div class="alert alert-success alert-dismissible" style="display: none;" id="truemsg">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    New Notice Successfully added
                </div>

                <form class="centre " action="" method="POST">
                    <div>
                        <h4><strong> Post a new notice</strong></h4>
                    </div>
                    <div>

                        <input id="subject" placeholder="Subject" type="text" name="subject" style="margin:auto">

                    </div>

                    <div id="file" class="form-group">
                        <style>
                            #file {
                                margin-left: 40px;
                                margin-top: 20px;
                            }
                        </style>
                        <!-- <label style="color: red;">Attachment</label> -->
                        <input type="file" name="resume" class="btn btn-flat btn-primary">
                    </div>

                    <br>
                    <div class="form-group mt-3">
                        <textarea style="top:80px " type="input" class="input" name="input" id="input" placeholder="Notice" required></textarea>
                    </div>

                    <div class="form-group text-center option">
                        <label>Audience </label>
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true" class="input" name="audience">

                            <option class="option" value="All Students">All Students</option>
                            <option class="option" value="Co-ordinators">Co-ordinators</option>


                        </select>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary btn-sm" id="submit" name="submit" type=" submit1">NOTIFY</button>

                    </div><br>
                    <div>
                    </div>


                </form>


        </div>
        </section>



        <div class="col-xs-5 responsive2 ">


            <div class="box box-primary ">
                <div class="box-header with-border">
                    <h3 class="box-title">Posted Notice</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Notice</th>

                                <th>Audience</th>

                                <th>File</th>

                                <th>Date and Time</th>
                                <th>Delete</th>



                            </tr>
                        </thead>
                        <tbody>


                            <?php

                            $sql = "SELECT * FROM notice";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {

                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <td><?php echo $row['subject']; ?></td>
                                    <td><?php echo $row['notice']; ?></td>
                                    <td><?php echo $row['audience']; ?></td>
                                    <?php if (isset($row['resume']) && !empty($row['resume'])) { ?>
    <td><a href="../uploads/resume/<?php echo $row['resume']; ?>" download="<?php echo 'Notice'; ?>"><i class="fa fa-file"></i></a></td>
<?php } else { ?>
    <td>No Resume Uploaded</td>
<?php } ?>

                                    <td><?php echo $row['date']; ?></td>

                                    <td><a id="delete" href="deletenotice.php?id=<?php echo $row['id']; ?>"><i class="fa fa-trash"></i></a></td>
                                    </tr><?php

                                        }
                                    }

                                            ?>


                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>

        </div>

    </div>


    <footer class="main-footer" style="margin:auto;margin-bottom: 0px; padding:15px;
  width: 100%;
  height: 50px; position:absolute; background-color:#1f0a0a; color:white">
        <div class="text-center">
            <strong>Copyright &copy; 2022 Placement Portal</strong> All rights
            reserved.
        </div>
    </footer>

</body>

</html>


<style>
    body {

        /* background-color: #bccde5;
         */
        background-color: white;
    }

    .centre {
        margin: 20px 30px 100px 20px;
        text-align: center;
        height: 450px;
        width: 700px;
        border: 2px solid black;
        border-radius: 10px;
        /* display: inline-grid; */
        display: inline-block;


    }

    #subject {

        width: 86%;


    }

    .option {
        width: 30%;
        margin: auto;
    }

    .input {

        height: 200px;
        width: 600px;
        border-radius: 5px;
        background-color: white;
        text-align: center;


    }

    .button {


        background-color: #3e79c8;

        /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 0px 10px 0px 10px;
    }

    @media screen and (max-width: 1447px) {

        .input1 {
            width: auto;
            height: auto;
        }

        .centre {

            height: 105%;
            width: 105%;
            margin-left: 100px;

        }

        .responsive2 {
            margin: auto;
            display: block;
            height: 80%;
            width: 80%;
            margin: auto;
        }

        #subject {
            height: 60%;
            width: 60%;
            margin: auto;

        }

        .option {
            height: 60%;
            width: 60%;
            margin: auto;
        }

        .input {
            height: 80%;
            width: 60%;
            margin: auto;

        }


    }
</style>