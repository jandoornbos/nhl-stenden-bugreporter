<?php
require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="nl">
    <head>
        <title>Bugreporter</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <span class="navbar-brand mb-0 h1">Bugreporter</span>
        </nav>
        <div class="container mt-3">
            <?php
            if (isset($_SESSION["success"]))
            {
                echo "<div class=\"alert alert-success\">";
                echo $_SESSION["success"];
                echo "</div>";
                unset($_SESSION["success"]);
            }
            if (isset($_SESSION["error"]))
            {
                echo "<div class=\"alert alert-danger\">";
                echo $_SESSION["error"];
                echo "</div>";
                unset($_SESSION["error"]);
            }
            ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (isset($_GET["p"]))
                    {
                        if (file_exists("views/" . $_GET["p"] . ".php"))
                        {
                            include("views/" . $_GET["p"] . ".php");
                        }
                        else
                        {
                            include("views/404.php");
                        }
                    }
                    else
                    {
                        include("views/list.php");
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
mysqli_close($db);