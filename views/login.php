<div class="jumbotron">
    <h1 class="display-4">Bugreporter</h1>
    <p>Welcome to the bugreporter. You need to sign in to get access.</p>
    <hr>
    <form method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Username">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <input type="hidden" name="action" value="login">
        <button type="submit" class="btn btn-success">Login</button>
        <a href="index.php?p=register" class="btn btn-secondary">Register</a>
    </form>
</div>