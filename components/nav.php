<?php include_once 'header.php'; ?>

<span class="logo"><a href="/books/">BOOKS</a></span>
<ul id="menu">
    <li class="item"><a href="new/"><i class="fas fa-plus-circle"></i> Add book</a></li>
    <!-- <li class="item button"><a href="../users/">Log In</a></li> -->
    <li class="item">
        <a href="/users/profile/">
            <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
        </a>
    </li>
    <li class="item button"><a href="logout/">Logout</a></li>
    

</ul>