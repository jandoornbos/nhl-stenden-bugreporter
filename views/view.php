<?php
$bug = getBug($_GET["id"]);
if (null === $bug)
{
    include('404.php');
    die();
}
?>
<h2 class="mb-3"><a href="index.php">&leftarrow;</a> Bug bekijken #<?php echo $bug["id"]; ?></h2>
<div class="card bg-dark text-white p-5">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-borderless text-white">
                <tr>
                    <th>ID</th>
                    <td colspan="3"><?php echo $bug["id"]; ?></td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td><?php echo $bug["productName"]; ?></td>
                    <th>Version</th>
                    <td><?php echo $bug["productVersion"]; ?></td>
                </tr>
                <tr>
                    <th>Hardware</th>
                    <td><?php echo $bug["hardware"]; ?></td>
                    <th>Freqency</th>
                    <td><?php echo $bug["frequency"]; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>Solution</h3>
            <?php echo $bug["proposedSolution"]; ?>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <a href="?p=edit&id=<?php echo $bug["id"]; ?>" class="btn btn-primary">Edit</a>
        <a href="?a=remove" class="btn btn-danger">Delete</a>
        <a href="#" class="btn btn-success">Solved</a>
    </div>
</div>
