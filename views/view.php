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
            <div class="btn-group float-right" role="group">
                <button type="button" class="btn btn-link text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="?p=edit&id=<?php echo $bug["id"]; ?>"><i class="fas fa-edit"></i> Edit</a>
                    <a class="dropdown-item" href="?a=remove&id=<?php echo $bug["id"]; ?>"><i class="fas fa-trash"></i> Remove</a>
                    <a class="dropdown-item" href="?a=solve&id=<?php echo $bug["id"]; ?>"><i class="fas fa-check"></i> Solve</a>
                </div>
            </div>
            <h3>General information</h3>
            <table class="table table-borderless text-white details">
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
    <div class="row mt-4">
        <div class="col-md-12">
            <h3>Solution</h3>
            <?php echo $bug["proposedSolution"]; ?>
        </div>
    </div>
</div>
