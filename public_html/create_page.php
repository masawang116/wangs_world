<?php
    session_start();
    require_once '../includes/functions.php';
    confirm_logout();
    include '../includes/layouts/header.php'; 
?>
<nav>
    <?php
    output_web_pages($db);
    ?>
</nav>
<header>
    <h1>Create New Page</h1>
</header>
<div id ='content'>
<?php
    if(isset($_POST['submit'])) {
        $parent_id = get_post_value('page_parent_id');
        $author_id = get_post_value('author_id');
        $name = mysqli_real_escape_string($db, get_post_value('name'));
        $date_created = date('Y\-m\-d');
        $date_last_modified = date('Y\-m\-d');
        $content = mysqli_real_escape_string($db, get_post_value('content'));

        if(trim($name) == '') {
            $name = 'Untitled Page';
        }
        if(trim($content) == '') {
            $content = 'No content.';
        }
        
        set_pages($db, $parent_id, $author_id, $name, $date_created, 
                $date_last_modified, $content); {
            
        }
        
        $index_filename = get_index_filename();
        $next_id = get_next_auto_increment_id($db, 'pages');
        header('Location: ' . $index_filename . '?page_id=' . $next_id);
    }
    
    $page_parent_id = get_get_value('page_parent_id');
    if($page_parent_id == 0) {
        $page_parent_id = 1;
    }
    $page_parent_name = get_page_name($db, $page_parent_id);
    $filename = get_filename();
?>
    
    <form action='<?php echo $filename; ?>' method='post'>
        <label Parent page: </label>
        <br />
        <input disabled type ='text' value = '<?php echo $page_parent_name; ?>'>
        <input type ='hidden' name='page_parent_id' value ='<?php echo $page_parent_id; ?>'>
        
        <br />
        
        <label for ='name'>Title: </label>
        <br />
        <input name ='name' type ='text' value = ''>
        <br />
        
        <label for ='content'>Content: </label>
        <br />
        <textarea name ='content' rows='6' value = ''></textarea>
        <br />
        
        <input hidden ='author_id' value ='1'>
        
        <input name ='submit' type='submit' value='Create Page'>
    </form>
</div>
<?php include '../includes/layouts/footer.php';