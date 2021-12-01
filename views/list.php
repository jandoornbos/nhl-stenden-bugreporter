<h2 class="mb-3">Buglist <a href="?p=edit" class="btn btn-success float-right mt-1"><i class="fas fa-plus"></i> Bug toevoegen</a></h2>
<table class="table table-striped table-hover align-middle">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Productname</th>
            <th>Status</th>
            <th class="actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach (getBugs() as $bug)
    {
        echo "<tr>";
        echo "<td>" . $bug["id"] . "</td>";
        echo "<td>" . $bug["productName"] . "</td>";
        echo "<td>" . (($bug["solved"] == 0) ? "Open" : "Solved") . "</td>";
        echo "<td class=\"actions\">";
        echo "<a href=\"?p=view&id=" . $bug["id"] ."\" class=\"btn btn-info btn-sm\"><i class=\"fas fa-eye\"></i></a> ";
        echo "<a href=\"?p=edit&id=" . $bug["id"] ."\" class=\"btn btn-secondary btn-sm\"><i class=\"fas fa-edit\"></i></a> ";
        echo "<a href=\"?a=solve&id=" . $bug["id"] ."\" class=\"btn btn-success btn-sm\"><i class=\"fas fa-check\"></i></a> ";
        echo "<a href=\"?a=remove&id=" . $bug["id"] . "\" class=\"btn btn-danger btn-sm\"><i class=\"fas fa-trash\"></i></a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>