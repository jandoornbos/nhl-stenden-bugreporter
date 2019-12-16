<?php
requiresLogin();

$pageType = "Add";
$submitAction = "add";

if (isset($_GET["id"]) && $_GET["id"] > 0)
{
    $pageType = "Edit";
    $submitAction = "update";

    $bug = getBug($_GET["id"]);
    if (null === $bug)
    {
        include('404.php');
        die();
    }
}
else
{
    // It's a new submission, and something went wrong, so put the $_POST data into the bug variable.
    // This way the fields are automatically filled again.
    $bug = $_POST;
}
?>
<h2 class="mb-3"><a href="index.php">&leftarrow;</a> <?php echo $pageType ?> bug #<?php echo $bug["id"]; ?></h2>
<div class="card">
    <div class="card-body">
        <form method="post">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="productName">Product name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="productName" value="<?php echo $bug["productName"]; ?>" id="productName">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="hardware">Hardware</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="hardware" value="<?php echo $bug["hardware"]; ?>" id="hardware">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="version">Version</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="productVersion" value="<?php echo $bug["productVersion"]; ?>" id="version">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="frequency">Frequency</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="frequency" value="<?php echo $bug["frequency"]; ?>" id="frequency">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="solution">Solution</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="proposedSolution" id="solution"><?php echo $bug["proposedSolution"]; ?></textarea>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $bug["id"]; ?>">
            <input type="hidden" name="action" value="<?php echo $submitAction; ?>">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
        </form>
    </div>
</div>
