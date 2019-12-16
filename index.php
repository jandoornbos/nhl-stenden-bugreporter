<?php
require_once('includes/functions.php');
?>
<!DOCTYPE html>
<html lang="nl">
    <head>
        <title>Bugreporter</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
              integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/a076d05399.js" async></script>
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <span class="navbar-brand mb-0 h1">Bugreporter</span>
            <?php if (isUserLoggedIn()): ?>
            <span class="navbar-text">Logged in as <?php echo getLoggedInUser()["email"]; ?>. <a href="index.php?a=logout">Logout</a></span>
            <?php endif; ?>
        </nav>
        <div class="container mt-3">
            <?php
            if (isset($messages["success"]))
            {
                echo "<div class=\"alert alert-success\">";
                echo $messages["success"];
                echo "</div>";
                setMessagesShown(true);
            }
            if (isset($messages["error"]))
            {
                echo "<div class=\"alert alert-danger\">";
                echo $messages["error"];
                echo "</div>";
                setMessagesShown(true);
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