<?php
    session_start();
    require_once '../includes/functions.php';
    require_once '../includes/connect_to_db.php';
    if(isset($_POST['submit'])) {
        $page_id = get_post_value('page_id');
        $parent_id = get_post_value('page_parent_id');
        $author_id = get_post_value('page_author_id');
        $name = mysqli_real_escape_string($db, get_post_value('page_name'));
        $date_created = get_post_value('page_date_created');
        $date_last_modified = date('Y\-m\-d');
        $content = mysqli_real_escape_string($db, get_post_value('page_content'));
        
        update_pages($db, $page_id, $parent_id, $author_id, $name, $date_created, $date_last_modified, $content);
        
        $index_filename = get_index_filename();
        header('Location: ' . $index_filename . '?page_id=' . $page_id);
    }
    $page_id = get_page_id();
    if($page_id == 0) {
        $index_filename = get_index_filename();
        echo "No valid file selected to edit.<br />Redirecting to <a href ='{$index_filename}'>main page</a> in 5 seconds.";
        header('Refresh: 5; URL= ' . $index_filename);
        exit();
    }
    include '../includes/layouts/header.php';
    $page_name = get_page_name($db, $page_id);
    $page_parent_id = get_page_parent_id($db, $page_id);
    $page_parent_name = get_page_name($db, $page_parent_id);
    if($page_parent_id == 0) {
        $page_parent_name = 'N/A';
    }
    $page_author_id = get_page_author_id($db, $page_id);
    $page_content = get_page_content($db, $page_id);
    $page_date_created = get_page_date_created($db, $page_id);
    
    $filename = get_filename();
?>
<nav>
    <?php output_web_pages($db); ?>
</nav>
<header>
    <h1>Editing '<?php echo $page_name; ?>'</h1>
</header>
<div id='content'>
    <form action='<?php echo $filename;?>' method='post'>
        <label>Parent Page:</label>
        <br />
        <input disabled type='text' value ='<?php echo $page_parent_name?>'>
        <input name ='page_parent_id' type ='hidden' value = '<?php echo $page_parent_id?>'>
        <br />
        <label for='page_name'>Title: </label>
        <br />
        <input name ='page_name' type ='text' value ='<?php echo $page_name; ?>'>
        <br />
        <label for='page_content'>Content: </label>
        <br />
        <textarea name ='page_content' rows ='6'><?php echo $page_content; ?></textarea>
        <br />
        <input name='page_author_id' type ='hidden' value ='<?php echo $page_author_id; ?>'>
        <input name ='page_date_created' type ='hidden' value='<?php echo $page_date_created; ?>'>
        <input name ='page_id' type ='hidden' value='<?php echo $page_id;?>'>
        <input name='submit' type='submit' value='Edit Page'>
    </form>
</div>
<?php
    include '../includes/layouts/footer.php';
    