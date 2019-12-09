<h2 class="mb-3">Buglist <a href="?p=edit" class="btn btn-success pull-right"><i class="fas fa-plus"></i> Bug toevoegen</a></h2>
<table class="table table-striped table-hover align-middle">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Productname</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach (getBugs() as $bug)
    {
        echo "<tr>";
        echo "<td>" . $bug['id'] . "</td>";
        echo "<td>" . $bug['productName'] . "</td>";
        echo "<td>";
        echo "<a href=\"?p=view&id=" . $bug['id'] ."\" class=\"btn btn-info btn-sm\"><i class=\"fas fa-eye\"></i> View bug</a> ";
        echo "<a href=\"?p=edit&id=" . $bug['id'] ."\" class=\"btn btn-secondary btn-sm\"><i class=\"fas fa-edit\"></i> Update bug</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>