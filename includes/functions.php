<?php
/*
 * functions.php
 * contains-
 *   outer functions
 *   inner functions
 */

//Outer functions- main pages call these

/**
 * connects to db
 * 
 * defines constants DB_HOST, DB_USER, DB_PASS, DB_NAME
 * 
 * returns mysqli_connect variable $db
 */

function set_pages($db, $parent_id, $author_id, $name, $date_created, $date_last_modified, $content){
    //todo parameterized query, stmt
    $query = "INSERT INTO pages "
            . "(parent_id, author_id, name, date_created, date_last_modified, content) VALUES "
            . "('{$parent_id}', '{$author_id}', '{$name}', '{$date_created}', '{$date_last_modified}', '{$content}')";

    $result = mysqli_query($db, $query);
    confirm_result($result);
}

function get_next_auto_increment_id($db, $table_name) {
    $query = "SHOW TABLE STATUS LIKE 'pages'";
    $result = mysqli_query($db, $query);
    confirm_result($result);
    $row = mysqli_fetch_assoc($result);
    return $row['Auto_increment'];
}

function format_text($text) {
    return preg_replace('/\r?\n|\r/','<br/>', $text);
}

function get_web_page_name_from_raw_name($db, $raw_name) {
    return get_id_from_id($db, 'web_pages', 'raw_name', $raw_name, 'name');
}

function get_web_pages_raw_names($db) {
    $web_pages = array();
    $result = get_result($db, 'web_pages');
    while($row = mysqli_fetch_assoc($result)) {
        array_push($web_pages, $row['raw_name']);
    }
    return $web_pages;
}

function get_index_filename(){
    return 'index.php';
}

function get_filename(){
    return basename($_SERVER['PHP_SELF']);
}

function get_create_page_filename() {
    return 'create_page.php';
}

function get_update_page_filename() {
    return 'edit_page.php';
}

/**
 * checks if logout is set to 1 in GET[]
 * 
 * unsets session username if it exists
 */
function confirm_logout()
{
    $logout_value = get_get_value('logout');
    if($logout_value == 1)
    {
        echo 'Logged out.<br />';
        if(isset($_SESSION['username'])) { 
            unset($_SESSION['username']);
        } else {
            echo 'Error: logout called even though session username not defined.<br />';
        }
    }
}
 
function output_web_pages($db) {
    $web_pages = get_web_pages_raw_names($db);
    echo '<ul>';
        for($i = 0; $i < count($web_pages); $i++) {       
            echo "<li><a href = '{$web_pages[$i]}'>{$web_pages[$i]}" . '</a></li>';
        }
    echo '</ul>';
}

function output_parents($db, $page_id){
    $output = array();
    array_push($output, $page_id);
    
    $id = $page_id;
    do {
        $parent_id = get_page_parent_id($db, $id);
        array_push($output, $parent_id);
        $id = $parent_id;
    } while ($parent_id != 0);
    $output = array_reverse($output);
    
    for($i = 1; $i < count($output); $i++) {
        $filename = get_filename();
        $id = $output[$i];
        $name = get_page_name($db, $id);
        echo "<a href = '{$filename}?page_id={$id}'>" . $name . '</a>';;
        
        if($i != count($output) - 1) {
            echo ' > ';
        }
    }
}

/*
 * executes deep inner expand and inner expand
 */
function full_expand($db, $page_grandparent_id, $page_parent_id, $page_id) {
    //sloppy
    echo '<ul>';
    if(!($page_id == 1)) {
        deep_inner_expand($db, $page_grandparent_id, $page_parent_id, $page_id);
    } else {
        inner_expand($db, $page_parent_id, $page_id);
    }
    echo '</ul>';
}

/*
 * expands the page's children and the children of the id
 */
function deep_inner_expand($db, $outer_page_id, $inner_page_id, $deep_inner_page_id) {
    $filename = get_filename();
    $query = "SELECT * FROM pages WHERE parent_id = {$outer_page_id}";
    $result = mysqli_query($db, $query);
    if ($result) {
        while($row = mysqli_fetch_assoc($result))
        {
            if($row['id'] == $inner_page_id) {
                echo '<li>';
                echo "<a href = '{$filename}?page_id={$row['id']}'>" . $row['name'] . '</a>';
                echo '</li>';
                if($row['id'] == $inner_page_id) {
                    echo '<ul>';
                    inner_expand($db, $inner_page_id, $deep_inner_page_id);
                    echo '</ul>';
                }
            }
        }
    } else {
        echo 'This page does not have a parent page.';
    }
}

/*
 * expands the page's children and the children of the id
 */
function inner_expand($db, $outer_page_id, $inner_page_id) {
    $filename = get_filename();
    $query = "SELECT * FROM pages WHERE parent_id = {$outer_page_id}";
    $result = mysqli_query($db, $query);
    if ($result) {
        while($row = mysqli_fetch_assoc($result))
        {
            echo '<li>';
            echo "<a href = '{$filename}?page_id={$row['id']}'>" . $row['name'] . '</a>';
            echo '</li>';
            if($row['id'] == $inner_page_id) {
                echo '<ul>';
                expand($db, $inner_page_id);
                echo '</ul>';
            }
        }
    } else {
        echo 'This page does not have a parent page.';
    }
}

/*
 * xpands the page's children
 */
function expand($db, $page_id) {
    $filename = get_filename();
    $create_page_filename = get_create_page_filename();
    $query = "SELECT * FROM pages WHERE parent_id = {$page_id}";
    $result = mysqli_query($db, $query);
    if ($result) {
        while($row = mysqli_fetch_assoc($result))
        {
            echo '<li>';
            echo "<a href = '{$filename}?page_id={$row['id']}'>" . $row['name'] . '</a>';
            echo '</li>';
        }
        echo "<li><a href = '{$create_page_filename}?page_parent_id={$page_id}'>" . 'Create New Page' . '</a></li>';
    }
}

/**
 * Gets the current page id from GET['page_id']
 */
function get_page_id()
{
    return get_get_value('page_id');
}

/**
 * Gets page name from id
 */
function get_page_name($db, $page_id)
{       
    return get_id_from_id($db, 'pages', 'id', $page_id, 'name');        
}

function get_page_content($db, $page_id)
{       
    return get_id_from_id($db, 'pages', 'id', $page_id, 'content');        
}

/**
 * Gets parent page's id, returns 0 and outputs error message if does not exist
 */
function get_page_parent_id($db, $page_id)
{       
    return get_id_from_id($db, 'pages', 'id', $page_id, 'parent_id');        
}

/**
 * Gets page's author id
 */
function get_page_author_id($db, $page_id)
{       
    return get_id_from_id($db, 'pages', 'id', $page_id, 'author_id');
}

//Inner functions - outer functions call these

/**
 * get GET value, gets GET[$value]
 * 
 * i.e get_get_value('page_id')
 * 
 *   ...
 * 
 *   returns $_GET['page_id']
 */
function get_get_value($value) {
    if( isset( $_GET[$value] ) )
    {
        $r = $_GET[$value];
    } else {
        $r = 0;
    }
    return $r;
}

function get_post_value($value) {
    if( isset( $_POST[$value] ) )
    {
        $r = $_POST[$value];
    } else {
        $r = 0;
    }
    return $r;
}

/*
 * variation of get_id_from_id
 * returns mysqli_query instead of one value
 */
function get_result_from_id($db, $table, $give_field, $give_id) {
    $query = "SELECT * FROM {$table} WHERE {$give_field} = {$give_id}";
    $result = mysqli_query($db, $query);
    confirm_result($result);
    return $result;
}

/**
 *   gets database id using given id
 * 
 * i.e get_id_from_id($db, 'pages', 'id', $page_id, 'author_id')
 * 
 *   query outputs "SELECT * FROM pages WHERE id = $page_id"
 * 
 *   ...
 * 
 *   returns $row['author_id']
 */
function get_id_from_id($db, $table, $give_field, $give_value, $receive_field) {
    $query = "SELECT * FROM {$table} WHERE {$give_field} = '{$give_value}'";
    $result = mysqli_query($db, $query);
    confirm_result($result);
    $row = mysqli_fetch_assoc($result);
    if( isset( $row[$receive_field] ) ) {
        return $row[$receive_field];
    } else {
        return 0;
    }
}

function get_result($db, $table) {
    $query = "SELECT * FROM {$table}";
    $result = mysqli_query($db, $query);
    return $result;
}


/**
 * checks if $result exists, used for mysqli results
 */
function confirm_result($result) {
    if(!$result) {
        die('MySQL database query failed.');
    }
}