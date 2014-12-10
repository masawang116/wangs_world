<?php 
    session_start();
    require_once('../includes/functions.php');
    confirm_logout();
    include '../includes/layouts/header.php'; 
?>
<?php
    $page_id = get_page_id();
    if($page_id == 0) {
        $page_id = 1;
    }
    $page_name = get_page_name($db, $page_id);
    
    $page_parent_id = get_page_parent_id($db, $page_id);
    
    $page_author_id = get_page_author_id($db, $page_id);
    $page_author_name = get_page_name($db, $page_author_id);
    if($page_author_name == 0) {
        $page_author_name = 'Anonymous';
    }
    
    $page_content = get_page_content($db, $page_id);
    
    $page_grandparent_id = get_page_parent_id($db, $page_parent_id);
    
    /*
    echo 'page id = ' . $page_id . '<br />';
    echo 'parent page id = ' . $page_parent_id . '<br />';
    echo 'author page id = ' . $page_author_id . '<br />';
     */
?>
<nav>
    <?php 
    output_web_pages($db);
    output_parents($db, $page_id);
    echo '<br />';
    full_expand($db, $page_grandparent_id, $page_parent_id, $page_id);
    ?>
</nav>
<header>
    <?php
        echo "<h1>{$page_name}</h1>";
        $edit_page_filename = get_update_page_filename();
        echo "<p><a href = {$edit_page_filename}?page_id={$page_id}>[Edit]</a></p>";
        echo "<p>by {$page_author_name}";
    ?>
</header>
<div id = 'content'>
    <?php
        $page_content = format_text($page_content);
        echo "<p>{$page_content}</p>"
    ?>
</div>
<?php
include '../includes/layouts/footer.php';