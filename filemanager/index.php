<?php
/**
 * H3K | Tiny File Manager
 * CCP Programmers
 * http://fb.com/ccpprogrammers
 * https://github.com/prasathmani/tinyfilemanager
 */

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Report all errors except E_NOTICE and E_WARNING
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set("log_errors", 1);
ini_set("error_log", date("Ymd") . "-log.txt");

// Default language
$lang = 'en';

// Auth with login/password (set true/false to enable/disable it)
$use_auth = true;

// Users: array('Username' => 'Password', 'Username2' => 'Password2', ...), Password has to encripted into MD5
$auth_users = array(
    'admin' => '67f659bb82e9afcad77cf69a2de8503d', //admin
    'user' => '827ccb0eea8a706c4c34a16891f84e7b' //12345
);

// Readonly users (usernames array)
$readonly_users = array(
    'user'
);

// Show or hide files and folders that starts with a dot
$show_hidden_files = true;

// Enable highlight.js (https://highlightjs.org/) on view's page
$use_highlightjs = true;

// highlight.js style
$highlightjs_style = 'vs';

// Enable ace.js (https://ace.c9.io/) on view's page
$edit_files = true;

// Send files though mail
$send_mail = false;

// Send files though mail
$toMailId = ""; //yourmailid@mail.com

// Default timezone for date() and time() - http://php.net/manual/en/timezones.php
$default_timezone = 'Etc/UTC'; // UTC

// Root path for file manager
// $root_path = $_SERVER['DOCUMENT_ROOT'];
// $root_path = dirname(__FILE__);
$root_path = dirname(__FILE__) . '/../';

// Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
// Will not working if $root_path will be outside of server document root
$root_url = '';

// Server hostname. Can set manually if wrong
$http_host = $_SERVER['HTTP_HOST'];

// input encoding for iconv
$iconv_input_encoding = 'UTF-8';

// date() format for file modification date
$datetime_format = 'd.m.y H:i';

// allowed upload file extensions
$upload_extensions = ''; // 'gif,png,jpg'

// show or hide the left side tree view
$show_tree_view = false;

//Array of folders excluded from listing
$GLOBALS['exclude_folders'] = array();

// include user config php file
if (defined('FM_CONFIG') && is_file(FM_CONFIG)) {
    include(FM_CONFIG);
}

//--- EDIT BELOW CAREFULLY OR DO NOT EDIT AT ALL

// if fm included
if (defined('FM_EMBED')) {
    $use_auth = false;
} else {
    @set_time_limit(600);
    
    date_default_timezone_set($default_timezone);
    
    ini_set('default_charset', 'UTF-8');
    if (version_compare(PHP_VERSION, '5.6.0', '<') && function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }
    if (function_exists('mb_regex_encoding')) {
        mb_regex_encoding('UTF-8');
    }
    
    session_cache_limiter('');
    session_name('filemanager');
    session_start();
}

if (empty($auth_users)) {
    $use_auth = false;
}

$is_https = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

// clean and check $root_path
$root_path = rtrim($root_path, '\\/');
$root_path = str_replace('\\', '/', $root_path);
if (!@is_dir($root_path)) {
    echo "<h1>Root path \"{$root_path}\" not found!</h1>";
    exit;
}

// clean $root_url
$root_url = fm_clean_path($root_url);

// abs path for site
defined('FM_SHOW_HIDDEN') || define('FM_SHOW_HIDDEN', $show_hidden_files);
defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
defined('FM_ROOT_URL') || define('FM_ROOT_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . (!empty($root_url) ? '/' . $root_url : ''));
defined('FM_SELF_URL') || define('FM_SELF_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . $_SERVER['PHP_SELF']);

// logout
if (isset($_GET['logout'])) {
    unset($_SESSION['logged']);
    fm_redirect(FM_SELF_URL);
}

// Show image here
if (isset($_GET['img'])) {
    fm_show_image($_GET['img']);
}

// Auth
if ($use_auth) {
    if (isset($_SESSION['logged'], $auth_users[$_SESSION['logged']])) {
        // Logged
    } elseif (isset($_POST['fm_usr'], $_POST['fm_pwd'])) {
        // Logging In
        sleep(1);
        if (isset($auth_users[$_POST['fm_usr']]) && md5($_POST['fm_pwd']) === $auth_users[$_POST['fm_usr']]) {
            $_SESSION['logged'] = $_POST['fm_usr'];
            fm_set_msg('You are logged in');
            fm_redirect(FM_SELF_URL . '?p=');
        } else {
            unset($_SESSION['logged']);
            fm_set_msg('Wrong password', 'error');
            fm_redirect(FM_SELF_URL);
        }
    } else {
        // Form
        unset($_SESSION['logged']);
        fm_show_header_login();
        fm_show_message();
?>
        <div class="path login-form">
                <img src="<?php
        echo FM_SELF_URL;
?>?img=logo" alt="File manager" width="159" style="margin:20px;">
            <form action="" method="post">
                <label for="fm_usr">Username</label><input type="text" id="fm_usr" name="fm_usr" value="" placeholder="Username" required><br>
                <label for="fm_pwd">Password</label><input type="password" id="fm_pwd" name="fm_pwd" value="" placeholder="Password" required><br>
                <input type="submit" value="Login">
            </form>
        </div>
        <?php
        fm_show_footer_login();
        exit;
    }
}

defined('FM_LANG') || define('FM_LANG', $lang);
defined('FM_EXTENSION') || define('FM_EXTENSION', $upload_extensions);
defined('FM_TREEVIEW') || define('FM_TREEVIEW', $show_tree_view);
define('FM_READONLY', $use_auth && !empty($readonly_users) && isset($_SESSION['logged']) && in_array($_SESSION['logged'], $readonly_users));
define('FM_IS_WIN', DIRECTORY_SEPARATOR == '\\');

// always use ?p=
if (!isset($_GET['p']) && empty($_FILES)) {
    fm_redirect(FM_SELF_URL . '?p=');
}

// get path
$p = isset($_GET['p']) ? $_GET['p'] : (isset($_POST['p']) ? $_POST['p'] : '');

// clean path
$p = fm_clean_path($p);

// instead globals vars
define('FM_PATH', $p);
define('FM_USE_AUTH', $use_auth);
define('FM_EDIT_FILE', $edit_files);
defined('FM_ICONV_INPUT_ENC') || define('FM_ICONV_INPUT_ENC', $iconv_input_encoding);
defined('FM_USE_HIGHLIGHTJS') || define('FM_USE_HIGHLIGHTJS', $use_highlightjs);
defined('FM_HIGHLIGHTJS_STYLE') || define('FM_HIGHLIGHTJS_STYLE', $highlightjs_style);
defined('FM_DATETIME_FORMAT') || define('FM_DATETIME_FORMAT', $datetime_format);

unset($p, $use_auth, $iconv_input_encoding, $use_highlightjs, $highlightjs_style);

/*************************** ACTIONS ***************************/

//AJAX Request
if (isset($_POST['ajax']) && !FM_READONLY) {
    
    //search : get list of files from the current folder
    if (isset($_POST['type']) && $_POST['type'] == "search") {
        $dir      = $_POST['path'];
        $response = scan($dir);
        echo json_encode($response);
    }
    
    //Send file to mail
    if (isset($_POST['type']) && $_POST['type'] == "mail") {
        //send mail Fn removed.
    }
    
    //backup files
    if (isset($_POST['type']) && $_POST['type'] == "backup") {
        $file    = $_POST['file'];
        $path    = $_POST['path'];
        $date    = date("dMy-His");
        $newFile = $file . '-' . $date . '.bak';
        copy($path . '/' . $file, $path . '/' . $newFile) or die("Unable to backup");
        echo "Backup $newFile Created";
    }
    
    exit;
}

// Delete file / folder
if (isset($_GET['del']) && !FM_READONLY) {
    $del = $_GET['del'];
    $del = fm_clean_path($del);
    $del = str_replace('/', '', $del);
    if ($del != '' && $del != '..' && $del != '.') {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        $is_dir = is_dir($path . '/' . $del);
        if (fm_rdelete($path . '/' . $del)) {
            $msg = $is_dir ? 'Folder <b>%s</b> deleted' : 'File <b>%s</b> deleted';
            fm_set_msg(sprintf($msg, fm_enc($del)));
        } else {
            $msg = $is_dir ? 'Folder <b>%s</b> not deleted' : 'File <b>%s</b> not deleted';
            fm_set_msg(sprintf($msg, fm_enc($del)), 'error');
        }
    } else {
        fm_set_msg('Wrong file or folder name', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Create folder
if (isset($_GET['new']) && isset($_GET['type']) && !FM_READONLY) {
    $new  = strip_tags($_GET['new']);
    $type = $_GET['type'];
    $new  = fm_clean_path($new);
    $new  = str_replace('/', '', $new);
    if ($new != '' && $new != '..' && $new != '.') {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        if ($_GET['type'] == "file") {
            if (!file_exists($path . '/' . $new)) {
                @fopen($path . '/' . $new, 'w') or die('Cannot open file:  ' . $new);
                fm_set_msg(sprintf('File <b>%s</b> created', fm_enc($new)));
            } else {
                fm_set_msg(sprintf('File <b>%s</b> already exists', fm_enc($new)), 'alert');
            }
        } else {
            if (fm_mkdir($path . '/' . $new, false) === true) {
                fm_set_msg(sprintf('Folder <b>%s</b> created', $new));
            } elseif (fm_mkdir($path . '/' . $new, false) === $path . '/' . $new) {
                fm_set_msg(sprintf('Folder <b>%s</b> already exists', fm_enc($new)), 'alert');
            } else {
                fm_set_msg(sprintf('Folder <b>%s</b> not created', fm_enc($new)), 'error');
            }
        }
    } else {
        fm_set_msg('Wrong folder name', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Copy folder / file
if (isset($_GET['copy'], $_GET['finish']) && !FM_READONLY) {
    // from
    $copy = $_GET['copy'];
    $copy = fm_clean_path($copy);
    // empty path
    if ($copy == '') {
        fm_set_msg('Source path not defined', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    // abs path from
    $from = FM_ROOT_PATH . '/' . $copy;
    // abs path to
    $dest = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $dest .= '/' . FM_PATH;
    }
    $dest .= '/' . basename($from);
    // move?
    $move = isset($_GET['move']);
    // copy/move
    if ($from != $dest) {
        $msg_from = trim(FM_PATH . '/' . basename($from), '/');
        if ($move) {
            $rename = fm_rename($from, $dest);
            if ($rename) {
                fm_set_msg(sprintf('Moved from <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)));
            } elseif ($rename === null) {
                fm_set_msg('File or folder with this path already exists', 'alert');
            } else {
                fm_set_msg(sprintf('Error while moving from <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)), 'error');
            }
        } else {
            if (fm_rcopy($from, $dest)) {
                fm_set_msg(sprintf('Copyied from <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)));
            } else {
                fm_set_msg(sprintf('Error while copying from <b>%s</b> to <b>%s</b>', fm_enc($copy), fm_enc($msg_from)), 'error');
            }
        }
    } else {
        fm_set_msg('Paths must be not equal', 'alert');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Mass copy files/ folders
if (isset($_POST['file'], $_POST['copy_to'], $_POST['finish']) && !FM_READONLY) {
    // from
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    // to
    $copy_to_path = FM_ROOT_PATH;
    $copy_to      = fm_clean_path($_POST['copy_to']);
    if ($copy_to != '') {
        $copy_to_path .= '/' . $copy_to;
    }
    if ($path == $copy_to_path) {
        fm_set_msg('Paths must be not equal', 'alert');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    if (!is_dir($copy_to_path)) {
        if (!fm_mkdir($copy_to_path, true)) {
            fm_set_msg('Unable to create destination folder', 'error');
            fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
        }
    }
    // move?
    $move   = isset($_POST['move']);
    // copy/move
    $errors = 0;
    $files  = $_POST['file'];
    if (is_array($files) && count($files)) {
        foreach ($files as $f) {
            if ($f != '') {
                // abs path from
                $from = $path . '/' . $f;
                // abs path to
                $dest = $copy_to_path . '/' . $f;
                // do
                if ($move) {
                    $rename = fm_rename($from, $dest);
                    if ($rename === false) {
                        $errors++;
                    }
                } else {
                    if (!fm_rcopy($from, $dest)) {
                        $errors++;
                    }
                }
            }
        }
        if ($errors == 0) {
            $msg = $move ? 'Selected files and folders moved' : 'Selected files and folders copied';
            fm_set_msg($msg);
        } else {
            $msg = $move ? 'Error while moving items' : 'Error while copying items';
            fm_set_msg($msg, 'error');
        }
    } else {
        fm_set_msg('Nothing selected', 'alert');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Rename
if (isset($_GET['ren'], $_GET['to']) && !FM_READONLY) {
    // old name
    $old  = $_GET['ren'];
    $old  = fm_clean_path($old);
    $old  = str_replace('/', '', $old);
    // new name
    $new  = $_GET['to'];
    $new  = fm_clean_path($new);
    $new  = str_replace('/', '', $new);
    // path
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    // rename
    if ($old != '' && $new != '') {
        if (fm_rename($path . '/' . $old, $path . '/' . $new)) {
            fm_set_msg(sprintf('Renamed from <b>%s</b> to <b>%s</b>', fm_enc($old), fm_enc($new)));
        } else {
            fm_set_msg(sprintf('Error while renaming from <b>%s</b> to <b>%s</b>', fm_enc($old), fm_enc($new)), 'error');
        }
    } else {
        fm_set_msg('Names not set', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Download
if (isset($_GET['dl'])) {
    $dl   = $_GET['dl'];
    $dl   = fm_clean_path($dl);
    $dl   = str_replace('/', '', $dl);
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    if ($dl != '' && is_file($path . '/' . $dl)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($path . '/' . $dl) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path . '/' . $dl));
        readfile($path . '/' . $dl);
        exit;
    } else {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
}

// Upload
if (!empty($_FILES) && !FM_READONLY) {
    $f    = $_FILES;
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    
    $errors  = 0;
    $uploads = 0;
    $total   = count($f['file']['name']);
    $allowed = (FM_EXTENSION) ? explode(',', FM_EXTENSION) : false;
    
    $filename      = $f['file']['name'];
    $tmp_name      = $f['file']['tmp_name'];
    $ext           = pathinfo($filename, PATHINFO_EXTENSION);
    $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;
    
    if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none' && $isFileAllowed) {
        $new = $path . '/' . $f['file']['name'];
        if (is_file($new) and file_exists($new)) {
            @file_put_contents($new, file_get_contents($tmp_name));
            @chown($path . '/' . $f['file']['name'], 'dosuser02');
            @chmod($path . '/' . $f['file']['name'], 0777);
            die('Successfully uploaded - edited');
        } elseif (move_uploaded_file($tmp_name, $path . '/' . $f['file']['name'])) {
            @chown($path . '/' . $f['file']['name'], 'dosuser02');
            @chmod($path . '/' . $f['file']['name'], 0777);
            die('Successfully uploaded');
        } else {
            die(sprintf('Error while uploading files. Uploaded files: %s', $uploads));
        }
    }
    exit();
}

// Mass deleting
if (isset($_POST['group'], $_POST['delete']) && !FM_READONLY) {
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    
    $errors = 0;
    $files  = $_POST['file'];
    if (is_array($files) && count($files)) {
        foreach ($files as $f) {
            if ($f != '') {
                $new_path = $path . '/' . $f;
                if (!fm_rdelete($new_path)) {
                    $errors++;
                }
            }
        }
        if ($errors == 0) {
            fm_set_msg('Selected files and folder deleted');
        } else {
            fm_set_msg('Error while deleting items', 'error');
        }
    } else {
        fm_set_msg('Nothing selected', 'alert');
    }
    
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Pack files
if (isset($_POST['group'], $_POST['zip']) && !FM_READONLY) {
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    
    if (!class_exists('ZipArchive')) {
        fm_set_msg('Operations with archives are not available', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    $files = $_POST['file'];
    if (!empty($files)) {
        chdir($path);
        
        if (count($files) == 1) {
            $one_file = reset($files);
            $one_file = basename($one_file);
            $zipname  = $one_file . '_' . date('ymd_His') . '.zip';
        } else {
            $zipname = 'archive_' . date('ymd_His') . '.zip';
        }
        
        $zipper = new FM_Zipper();
        $res    = $zipper->create($zipname, $files);
        
        if ($res) {
            fm_set_msg(sprintf('Archive <b>%s</b> created', fm_enc($zipname)));
        } else {
            fm_set_msg('Archive not created', 'error');
        }
    } else {
        fm_set_msg('Nothing selected', 'alert');
    }
    
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Unpack
if (isset($_GET['unzip']) && !FM_READONLY) {
    $unzip = $_GET['unzip'];
    $unzip = fm_clean_path($unzip);
    $unzip = str_replace('/', '', $unzip);
    
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    
    if (!class_exists('ZipArchive')) {
        fm_set_msg('Operations with archives are not available', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    if ($unzip != '' && is_file($path . '/' . $unzip)) {
        $zip_path = $path . '/' . $unzip;
        
        //to folder
        $tofolder = '';
        if (isset($_GET['tofolder'])) {
            $tofolder = pathinfo($zip_path, PATHINFO_FILENAME);
            if (fm_mkdir($path . '/' . $tofolder, true)) {
                $path .= '/' . $tofolder;
            }
        }
        
        $zipper = new FM_Zipper();
        $res    = $zipper->unzip($zip_path, $path);
        
        if ($res) {
            fm_set_msg('Archive unpacked');
        } else {
            fm_set_msg('Archive not unpacked', 'error');
        }
        
    } else {
        fm_set_msg('File not found', 'error');
    }
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

// Change Perms (not for Windows)
if (isset($_POST['chmod']) && !FM_READONLY && !FM_IS_WIN) {
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }
    
    $file = $_POST['chmod'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    $mode = 0;
    if (!empty($_POST['ur'])) {
        $mode |= 0400;
    }
    if (!empty($_POST['uw'])) {
        $mode |= 0200;
    }
    if (!empty($_POST['ux'])) {
        $mode |= 0100;
    }
    if (!empty($_POST['gr'])) {
        $mode |= 0040;
    }
    if (!empty($_POST['gw'])) {
        $mode |= 0020;
    }
    if (!empty($_POST['gx'])) {
        $mode |= 0010;
    }
    if (!empty($_POST['or'])) {
        $mode |= 0004;
    }
    if (!empty($_POST['ow'])) {
        $mode |= 0002;
    }
    if (!empty($_POST['ox'])) {
        $mode |= 0001;
    }
    
    if (@chmod($path . '/' . $file, $mode)) {
        fm_set_msg('Permissions changed');
    } else {
        fm_set_msg('Permissions not changed', 'error');
    }
    
    fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
}

/*************************** /ACTIONS ***************************/

// get current path
$path = FM_ROOT_PATH;
if (FM_PATH != '') {
    $path .= '/' . FM_PATH;
}

// check path
if (!is_dir($path)) {
    fm_redirect(FM_SELF_URL . '?p=');
}

// get parent folder
$parent = fm_get_parent_path(FM_PATH);

$objects = is_readable($path) ? scandir($path) : array();
$folders = array();
$files   = array();
if (is_array($objects)) {
    foreach ($objects as $file) {
        if ($file == '.' || $file == '..' && in_array($file, $GLOBALS['exclude_folders'])) {
            continue;
        }
        if (!FM_SHOW_HIDDEN && substr($file, 0, 1) === '.') {
            continue;
        }
        $new_path = $path . '/' . $file;
        if (is_file($new_path)) {
            $files[] = $file;
        } elseif (is_dir($new_path) && $file != '.' && $file != '..' && !in_array($file, $GLOBALS['exclude_folders'])) {
            $folders[] = $file;
        }
    }
}

if (!empty($files)) {
    natcasesort($files);
}
if (!empty($folders)) {
    natcasesort($folders);
}

// upload form
if (isset($_GET['upload']) && !FM_READONLY) {
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.js"></script>

    <div class="path">
        <p><b>Uploading files</b></p>
        <p class="break-word">Destination folder: <?php
    echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH));
?></p>
        <form action="<?php
    echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?p=' . fm_enc(FM_PATH);
?>" class="dropzone" id="fileuploader" enctype="multipart/form-data">
            <input type="hidden" name="p" value="<?php
    echo fm_enc(FM_PATH);
?>">
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>
        </form>

    </div>
    <?php
    fm_show_footer();
    exit;
}

// copy form POST
if (isset($_POST['copy']) && !FM_READONLY) {
    $copy_files = $_POST['file'];
    if (!is_array($copy_files) || empty($copy_files)) {
        fm_set_msg('Nothing selected', 'alert');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
?>
    <div class="path">
        <p><b>Copying</b></p>
        <form action="" method="post">
            <input type="hidden" name="p" value="<?php
    echo fm_enc(FM_PATH);
?>">
            <input type="hidden" name="finish" value="1">
            <?php
    foreach ($copy_files as $cf) {
        echo '<input type="hidden" name="file[]" value="' . fm_enc($cf) . '">' . PHP_EOL;
    }
?>
            <p class="break-word">Files: <b><?php
    echo implode('</b>, <b>', $copy_files);
?></b></p>
            <p class="break-word">Source folder: <?php
    echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH));
?><br>
                <label for="inp_copy_to">Destination folder:</label>
                <?php
    echo FM_ROOT_PATH;
?>/<input type="text" name="copy_to" id="inp_copy_to" value="<?php
    echo fm_enc(FM_PATH);
?>">
            </p>
            <p><label><input type="checkbox" name="move" value="1"> Move'</label></p>
            <p>
                <button type="submit" class="btn"><i class="fa fa-check-circle"></i> Copy </button> &nbsp;
                <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
            </p>
        </form>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// copy form
if (isset($_GET['copy']) && !isset($_GET['finish']) && !FM_READONLY) {
    $copy = $_GET['copy'];
    $copy = fm_clean_path($copy);
    if ($copy == '' || !file_exists(FM_ROOT_PATH . '/' . $copy)) {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
?>
    <div class="path">
        <p><b>Copying</b></p>
        <p class="break-word">
            Source path: <?php
    echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . $copy));
?><br>
            Destination folder: <?php
    echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH));
?>
        </p>
        <p>
            <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>&amp;copy=<?php
    echo urlencode($copy);
?>&amp;finish=1"><i class="fa fa-check-circle"></i> Copy</a></b> &nbsp;
            <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>&amp;copy=<?php
    echo urlencode($copy);
?>&amp;finish=1&amp;move=1"><i class="fa fa-check-circle"></i> Move</a></b> &nbsp;
            <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
        </p>
        <p><i>Select folder</i></p>
        <ul class="folders break-word">
            <?php
    if ($parent !== false) {
?>
                <li><a href="?p=<?php
        echo urlencode($parent);
?>&amp;copy=<?php
        echo urlencode($copy);
?>"><i class="fa fa-chevron-circle-left"></i> ..</a></li>
            <?php
    }
    foreach ($folders as $f) {
?>
                <li><a href="?p=<?php
        echo urlencode(trim(FM_PATH . '/' . $f, '/'));
?>&amp;copy=<?php
        echo urlencode($copy);
?>"><i class="fa fa-folder-o"></i> <?php
        echo fm_convert_win($f);
?></a></li>
            <?php
    }
?>
        </ul>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// file viewer
if (isset($_GET['view'])) {
    $file = $_GET['view'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file)) {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    
    $file_url  = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;
    
    $ext       = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize  = filesize($file_path);
    
    $is_zip   = false;
    $is_image = false;
    $is_audio = false;
    $is_video = false;
    $is_text  = false;
    
    $view_title = 'File';
    $filenames  = false; // for zip
    $content    = ''; // for text
    
    if ($ext == 'zip') {
        $is_zip     = true;
        $view_title = 'Archive';
        $filenames  = fm_get_zif_info($file_path);
    } elseif (in_array($ext, fm_get_image_exts())) {
        $is_image   = true;
        $view_title = 'Image';
    } elseif (in_array($ext, fm_get_audio_exts())) {
        $is_audio   = true;
        $view_title = 'Audio';
    } elseif (in_array($ext, fm_get_video_exts())) {
        $is_video   = true;
        $view_title = 'Video';
    } elseif (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }
    
?>
    <div class="path">
        <p class="break-word"><b><?php
    echo $view_title;
?> "<?php
    echo fm_enc(fm_convert_win($file));
?>"</b></p>
        <p class="break-word">
            Full path: <?php
    echo fm_enc(fm_convert_win($file_path));
?><br>
            File size: <?php
    echo fm_get_filesize($filesize);
?><?php
    if ($filesize >= 1000):
?> (<?php
        echo sprintf('%s bytes', $filesize);
?>)<?php
    endif;
?><br>
           MIME-type: <?php
    echo $mime_type;
?><br>
            <?php
    // ZIP info
    if ($is_zip && $filenames !== false) {
        $total_files  = 0;
        $total_comp   = 0;
        $total_uncomp = 0;
        foreach ($filenames as $fn) {
            if (!$fn['folder']) {
                $total_files++;
            }
            $total_comp += $fn['compressed_size'];
            $total_uncomp += $fn['filesize'];
        }
?>
                Files in archive: <?php
        echo $total_files;
?><br>
                Total size: <?php
        echo fm_get_filesize($total_uncomp);
?><br>
                Size in archive: <?php
        echo fm_get_filesize($total_comp);
?><br>
                Compression: <?php
        echo round(($total_comp / $total_uncomp) * 100);
?>%<br>
                <?php
    }
    // Image info
    if ($is_image) {
        $image_size = getimagesize($file_path);
        echo 'Image sizes: ' . (isset($image_size[0]) ? $image_size[0] : '0') . ' x ' . (isset($image_size[1]) ? $image_size[1] : '0') . '<br>';
    }
    // Text info
    if ($is_text) {
        $is_utf8 = fm_is_utf8($content);
        if (function_exists('iconv')) {
            if (!$is_utf8) {
                $content = iconv(FM_ICONV_INPUT_ENC, 'UTF-8//IGNORE', $content);
            }
        }
        echo 'Charset: ' . ($is_utf8 ? 'utf-8' : '8 bit') . '<br>';
    }
?>
        </p>
        <p>
            <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>&amp;dl=<?php
    echo urlencode($file);
?>"><i class="fa fa-cloud-download"></i> Download</a></b> &nbsp;
            <b><a href="<?php
    echo fm_enc($file_url);
?>" target="_blank"><i class="fa fa-external-link-square"></i> Open</a></b> &nbsp;
            <?php
    // ZIP actions
    if (!FM_READONLY && $is_zip && $filenames !== false) {
        $zip_name = pathinfo($file_path, PATHINFO_FILENAME);
?>
                <b><a href="?p=<?php
        echo urlencode(FM_PATH);
?>&amp;unzip=<?php
        echo urlencode($file);
?>"><i class="fa fa-check-circle"></i> UnZip</a></b> &nbsp;
                <b><a href="?p=<?php
        echo urlencode(FM_PATH);
?>&amp;unzip=<?php
        echo urlencode($file);
?>&amp;tofolder=1" title="UnZip to <?php
        echo fm_enc($zip_name);
?>"><i class="fa fa-check-circle"></i>
                    UnZip to folder</a></b> &nbsp;
                <?php
    }
    if ($is_text && !FM_READONLY) {
?>
            <b><a href="?p=<?php
        echo urlencode(trim(FM_PATH));
?>&amp;edit=<?php
        echo urlencode($file);
?>" class="edit-file"><i class="fa fa-pencil-square"></i> Edit</a></b> &nbsp;
            <b><a href="?p=<?php
        echo urlencode(trim(FM_PATH));
?>&amp;edit=<?php
        echo urlencode($file);
?>&env=ace" class="edit-file"><i class="fa fa-pencil-square"></i> Advanced Edit</a></b> &nbsp;
            <?php
    }
    if ($send_mail && !FM_READONLY) {
?>
            <b><a href="javascript:mailto('<?php
        echo urlencode(trim(FM_ROOT_PATH . '/' . FM_PATH));
?>','<?php
        echo urlencode($file);
?>')"><i class="fa fa-pencil-square"></i> Mail</a></b> &nbsp;
            <?php
    }
?>
            <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>"><i class="fa fa-chevron-circle-left"></i> Back</a></b>
        </p>
        <?php
    if ($is_zip) {
        // ZIP content
        if ($filenames !== false) {
            echo '<code class="maxheight">';
            foreach ($filenames as $fn) {
                if ($fn['folder']) {
                    echo '<b>' . fm_enc($fn['name']) . '</b><br>';
                } else {
                    echo $fn['name'] . ' (' . fm_get_filesize($fn['filesize']) . ')<br>';
                }
            }
            echo '</code>';
        } else {
            echo '<p>Error while fetching archive info</p>';
        }
    } elseif ($is_image) {
        // Image content
        if (in_array($ext, array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'ico'
        ))) {
            echo '<p><img src="' . fm_enc($file_url) . '" alt="" class="preview-img"></p>';
        }
    } elseif ($is_audio) {
        // Audio content
        echo '<p><audio src="' . fm_enc($file_url) . '" controls preload="metadata"></audio></p>';
    } elseif ($is_video) {
        // Video content
        echo '<div class="preview-video"><video src="' . fm_enc($file_url) . '" width="640" height="360" controls preload="metadata"></video></div>';
    } elseif ($is_text) {
        if (FM_USE_HIGHLIGHTJS) {
            // highlight
            $hljs_classes = array(
                'shtml' => 'xml',
                'htaccess' => 'apache',
                'phtml' => 'php',
                'lock' => 'json',
                'svg' => 'xml'
            );
            $hljs_class   = isset($hljs_classes[$ext]) ? 'lang-' . $hljs_classes[$ext] : 'lang-' . $ext;
            if (empty($ext) || in_array(strtolower($file), fm_get_text_names()) || preg_match('#\.min\.(css|js)$#i', $file)) {
                $hljs_class = 'nohighlight';
            }
            $content = '<pre class="with-hljs"><code class="' . $hljs_class . '">' . fm_enc($content) . '</code></pre>';
        } elseif (in_array($ext, array(
            'php',
            'php4',
            'php5',
            'phtml',
            'phps'
        ))) {
            // php highlight
            $content = highlight_string($content, true);
        } else {
            $content = '<pre>' . fm_enc($content) . '</pre>';
        }
        echo $content;
    }
?>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// file editor
if (isset($_GET['edit'])) {
    $file = $_GET['edit'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || !is_file($path . '/' . $file)) {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    
    $file_url  = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;
    
    //normal editer
    $isNormalEditor = true;
    if (isset($_GET['env'])) {
        if ($_GET['env'] == "ace") {
            $isNormalEditor = false;
        }
    }
    
    //Save File
    if (isset($_POST['savedata'])) {
        $writedata = $_POST['savedata'];
        $fd        = fopen($file_path, "w");
        @fwrite($fd, $writedata);
        fclose($fd);
        fm_set_msg('File Saved Successfully', 'alert');
    }
    
    $ext       = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize  = filesize($file_path);
    $is_text   = false;
    $content   = ''; // for text
    
    if (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }
    
?>
    <div class="path">
        <div class="edit-file-actions">
            <a title="Cancel" href="?p=<?php
    echo urlencode(trim(FM_PATH));
?>&amp;view=<?php
    echo urlencode($file);
?>"><i class="fa fa-reply-all"></i> Cancel</a>
            <a title="Backup" href="javascript:backup('<?php
    echo urlencode($path);
?>','<?php
    echo urlencode($file);
?>')"><i class="fa fa-database"></i> Backup</a>
            <?php
    if ($is_text) {
?>
                <?php
        if ($isNormalEditor) {
?>
                    <a title="Advanced" href="?p=<?php
            echo urlencode(trim(FM_PATH));
?>&amp;edit=<?php
            echo urlencode($file);
?>&amp;env=ace"><i class="fa fa-paper-plane"></i> Advanced Editor</a>
                    <button type="button" name="Save" data-url="<?php
            echo fm_enc($file_url);
?>" onclick="edit_save(this,'nrl')"><i class="fa fa-floppy-o"></i> Save</button>
                <?php
        } else {
?>
                    <a title="Plain Editor" href="?p=<?php
            echo urlencode(trim(FM_PATH));
?>&amp;edit=<?php
            echo urlencode($file);
?>"><i class="fa fa-text-height"></i> Plain Editor</a>
                    <button type="button" name="Save" data-url="<?php
            echo fm_enc($file_url);
?>" onclick="edit_save(this,'ace')"><i class="fa fa-floppy-o"></i> Save</button>
                <?php
        }
?>
            <?php
    }
?>
        </div>
        <?php
    if ($is_text && $isNormalEditor) {
        echo '<textarea id="normal-editor" rows="33" cols="120" style="width: 99.5%;">' . htmlspecialchars($content) . '</textarea>';
    } elseif ($is_text) {
        echo '<div id="editor" contenteditable="true">' . htmlspecialchars($content) . '</div>';
    } else {
        fm_set_msg('FILE EXTENSION HAS NOT SUPPORTED', 'error');
    }
?>
    </div>
    <?php
    fm_show_footer();
    exit;
}

// chmod (not for Windows)
if (isset($_GET['chmod']) && !FM_READONLY && !FM_IS_WIN) {
    $file = $_GET['chmod'];
    $file = fm_clean_path($file);
    $file = str_replace('/', '', $file);
    if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        fm_set_msg('File not found', 'error');
        fm_redirect(FM_SELF_URL . '?p=' . urlencode(FM_PATH));
    }
    
    fm_show_header(); // HEADER
    fm_show_nav_path(FM_PATH); // current path
    
    $file_url  = FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file;
    $file_path = $path . '/' . $file;
    
    $mode = fileperms($path . '/' . $file);
    
?>
    <div class="path">
        <p><b><?php
    echo 'Change Permissions';
?></b></p>
        <p>
            <?php
    echo 'Full path:';
?> <?php
    echo $file_path;
?><br>
        </p>
        <form action="" method="post">
            <input type="hidden" name="p" value="<?php
    echo fm_enc(FM_PATH);
?>">
            <input type="hidden" name="chmod" value="<?php
    echo fm_enc($file);
?>">

            <table class="compact-table">
                <tr>
                    <td></td>
                    <td><b>Owner</b></td>
                    <td><b>Group</b></td>
                    <td><b>Other</b></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Read</b></td>
                    <td><label><input type="checkbox" name="ur" value="1"<?php
    echo ($mode & 00400) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="gr" value="1"<?php
    echo ($mode & 00040) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="or" value="1"<?php
    echo ($mode & 00004) ? ' checked' : '';
?>></label></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Write</b></td>
                    <td><label><input type="checkbox" name="uw" value="1"<?php
    echo ($mode & 00200) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="gw" value="1"<?php
    echo ($mode & 00020) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="ow" value="1"<?php
    echo ($mode & 00002) ? ' checked' : '';
?>></label></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Execute</b></td>
                    <td><label><input type="checkbox" name="ux" value="1"<?php
    echo ($mode & 00100) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="gx" value="1"<?php
    echo ($mode & 00010) ? ' checked' : '';
?>></label></td>
                    <td><label><input type="checkbox" name="ox" value="1"<?php
    echo ($mode & 00001) ? ' checked' : '';
?>></label></td>
                </tr>
            </table>

            <p>
                <button type="submit" class="btn"><i class="fa fa-check-circle"></i> Change</button> &nbsp;
                <b><a href="?p=<?php
    echo urlencode(FM_PATH);
?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
            </p>

        </form>

    </div>
    <?php
    fm_show_footer();
    exit;
}

//--- FILEMANAGER MAIN
fm_show_header(); // HEADER
fm_show_nav_path(FM_PATH); // current path

// messages
fm_show_message();

$num_files      = count($files);
$num_folders    = count($folders);
$all_files_size = 0;
?>
<form action="" method="post">
    <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH);?>">
    <input type="hidden" name="group" value="1">
    <?php if (FM_TREEVIEW) {?>
        <div class="file-tree-view" id="file-tree-view">
            <div class="tree-title">Browse</div>
            <?php
                //file tre view
                echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "javascript:alert('You clicked on [link]');");
            ?>
        </div>
    <?php } ?>
    <table class="table" id="main-table"><thead><tr>
        <?php if (!FM_READONLY): ?>
            <th style="width:3%"><label><input type="checkbox" title="Invert selection" onclick="checkbox_toggle()"></label></th>
        <?php endif;?>
        <th>Name</th><th style="width:10%">Size</th>
        <th style="width:12%">Modified</th>
        <?php
        if (!FM_IS_WIN):
        ?><th style="width:6%">Perms</th><th style="width:10%">Owner</th><?php
        endif;
        ?>
        <th style="width:<?php
        if (!FM_READONLY):
        ?>13<?php
        else:
        ?>6.5<?php
        endif;
        ?>%">Actions</th></tr></thead>
        <?php
        // link to parent folder
        if ($parent !== false) {
        ?>
        <tr><?php
            if (!FM_READONLY):
        ?><td></td><?php
            endif;
        ?><td colspan="<?php
            echo !FM_IS_WIN ? '6' : '4';
        ?>"><a href="?p=<?php
            echo urlencode($parent);
        ?>"><i class="fa fa-chevron-circle-left"></i> ..</a></td></tr>
        <?php
        }
        foreach ($folders as $f) {
            $is_link = is_link($path . '/' . $f);
            $img     = $is_link ? 'icon-link_folder' : 'fa fa-folder-o';
            $modif   = date(FM_DATETIME_FORMAT, filemtime($path . '/' . $f));
            $perms   = substr(decoct(fileperms($path . '/' . $f)), -4);
            if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                $owner = posix_getpwuid(fileowner($path . '/' . $f));
                $group = posix_getgrgid(filegroup($path . '/' . $f));
            } else {
                $owner = array(
                    'name' => '?'
                );
                $group = array(
                    'name' => '?'
                );
            }
        ?>
        <tr>
        <?php
            if (!FM_READONLY):
        ?><td><label><input type="checkbox" name="file[]" value="<?php
                echo fm_enc($f);
        ?>"></label></td><?php
            endif;
        ?>
        <td><div class="filename"><a href="?p=<?php
            echo urlencode(trim(FM_PATH . '/' . $f, '/'));
        ?>"><i class="<?php
            echo $img;
        ?>"></i> <?php
            echo fm_convert_win($f);
        ?></a><?php
            echo ($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '');
        ?></div></td>
        <td>Folder</td><td><?php
            echo $modif;
        ?></td>
        <?php
            if (!FM_IS_WIN):
        ?>
        <td><?php
                if (!FM_READONLY):
        ?><a title="Change Permissions" href="?p=<?php
                    echo urlencode(FM_PATH);
        ?>&amp;chmod=<?php
                    echo urlencode($f);
        ?>"><?php
                    echo $perms;
        ?></a><?php
                else:
        ?><?php
                    echo $perms;
        ?><?php
                endif;
        ?></td>
        <td><?php
                echo $owner['name'] . ':' . $group['name'];
        ?></td>
        <?php
            endif;
        ?>
        <td class="inline-actions"><?php
            if (!FM_READONLY):
        ?>
        <a title="Delete" href="?p=<?php
                echo urlencode(FM_PATH);
        ?>&amp;del=<?php
                echo urlencode($f);
        ?>" onclick="return confirm('Delete folder?');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
        <a title="Rename" href="#" onclick="rename('<?php
                echo fm_enc(FM_PATH);
        ?>', '<?php
                echo fm_enc($f);
        ?>');return false;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
        <a title="Copy to..." href="?p=&amp;copy=<?php
                echo urlencode(trim(FM_PATH . '/' . $f, '/'));
        ?>"><i class="fa fa-files-o" aria-hidden="true"></i></a>
        <?php
            endif;
        ?>
        <a title="Direct link" href="<?php
            echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f . '/');
        ?>" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a>
        </td></tr>
            <?php
            flush();
        }

        foreach ($files as $f) {
            $is_link      = is_link($path . '/' . $f);
            $img          = $is_link ? 'fa fa-file-text-o' : fm_get_file_icon_class($path . '/' . $f);
            $modif        = date(FM_DATETIME_FORMAT, filemtime($path . '/' . $f));
            $filesize_raw = filesize($path . '/' . $f);
            $filesize     = fm_get_filesize($filesize_raw);
            $filelink     = '?p=' . urlencode(FM_PATH) . '&amp;view=' . urlencode($f);
            $all_files_size += $filesize_raw;
            $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
            if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                $owner = posix_getpwuid(fileowner($path . '/' . $f));
                $group = posix_getgrgid(filegroup($path . '/' . $f));
            } else {
                $owner = array(
                    'name' => '?'
                );
                $group = array(
                    'name' => '?'
                );
            }
        ?>
        <tr>
        <?php
            if (!FM_READONLY):
        ?><td><label><input type="checkbox" name="file[]" value="<?php
                echo fm_enc($f);
        ?>"></label></td><?php
            endif;
        ?>
        <td><div class="filename"><a href="<?php
            echo $filelink;
        ?>" title="File info"><i class="<?php
            echo $img;
        ?>"></i> <?php
            echo fm_convert_win($f);
        ?></a><?php
            echo ($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '');
        ?></div></td>
        <td><span title="<?php
            printf('%s bytes', $filesize_raw);
        ?>"><?php
            echo $filesize;
        ?></span></td>
        <td><?php
            echo $modif;
        ?></td>
        <?php
            if (!FM_IS_WIN):
        ?>
        <td><?php
                if (!FM_READONLY):
        ?><a title="<?php
                    echo 'Change Permissions';
        ?>" href="?p=<?php
                    echo urlencode(FM_PATH);
        ?>&amp;chmod=<?php
                    echo urlencode($f);
        ?>"><?php
                    echo $perms;
        ?></a><?php
                else:
        ?><?php
                    echo $perms;
        ?><?php
                endif;
        ?></td>
        <td><?php
                echo fm_enc($owner['name'] . ':' . $group['name']);
        ?></td>
        <?php
            endif;
        ?>
        <td class="inline-actions">
        <?php
            if (!FM_READONLY):
        ?>
        <a title="Delete" href="?p=<?php
                echo urlencode(FM_PATH);
        ?>&amp;del=<?php
                echo urlencode($f);
        ?>" onclick="return confirm('Delete file?');"><i class="fa fa-trash-o"></i></a>
        <a title="Rename" href="#" onclick="rename('<?php
                echo fm_enc(FM_PATH);
        ?>', '<?php
                echo fm_enc($f);
        ?>');return false;"><i class="fa fa-pencil-square-o"></i></a>
        <a title="Copy to..." href="?p=<?php
                echo urlencode(FM_PATH);
        ?>&amp;copy=<?php
                echo urlencode(trim(FM_PATH . '/' . $f, '/'));
        ?>"><i class="fa fa-files-o"></i></a>
        <?php
            endif;
        ?>
        <a title="Direct link" href="<?php
            echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f);
        ?>" target="_blank"><i class="fa fa-link"></i></a>
        <a title="Download" href="?p=<?php
            echo urlencode(FM_PATH);
        ?>&amp;dl=<?php
            echo urlencode($f);
        ?>"><i class="fa fa-download"></i></a>
        </td></tr>
            <?php
            flush();
        }

        if (empty($folders) && empty($files)) {
        ?>
        <tr><?php
            if (!FM_READONLY):
        ?><td></td><?php
            endif;
        ?><td colspan="<?php
            echo !FM_IS_WIN ? '6' : '4';
        ?>"><em><?php
            echo 'Folder is empty';
        ?></em></td></tr>
        <?php
        } else {
        ?>
        <tr><?php
            if (!FM_READONLY):
        ?><td class="gray"></td><?php
            endif;
        ?><td class="gray" colspan="<?php
            echo !FM_IS_WIN ? '6' : '4';
        ?>">
        Full size: <span title="<?php
            printf('%s bytes', $all_files_size);
        ?>"><?php
            echo fm_get_filesize($all_files_size);
        ?></span>,
        files: <?php
            echo $num_files;
        ?>,
        folders: <?php
            echo $num_folders;
        ?>
        </td></tr>
        <?php
        }
        ?>
    </table>
    <?php
    if (!FM_READONLY):
    ?>
    <p class="path footer-links"><a href="#/select-all" class="group-btn" onclick="select_all();return false;"><i class="fa fa-check-square"></i> Select all</a> &nbsp;
    <a href="#/unselect-all" class="group-btn" onclick="unselect_all();return false;"><i class="fa fa-window-close"></i> Unselect all</a> &nbsp;
    <a href="#/invert-all" class="group-btn" onclick="invert_all();return false;"><i class="fa fa-th-list"></i> Invert selection</a> &nbsp;
    <input type="submit" class="hidden" name="delete" id="a-delete" value="Delete" onclick="return confirm('Delete selected files and folders?')">
    <a href="javascript:document.getElementById('a-delete').click();" class="group-btn"><i class="fa fa-trash"></i> Delete </a> &nbsp;
    <input type="submit" class="hidden" name="zip" id="a-zip" value="Zip" onclick="return confirm('Create archive?')">
    <a href="javascript:document.getElementById('a-zip').click();" class="group-btn"><i class="fa fa-file-archive-o"></i> Zip </a> &nbsp;
    <input type="submit" class="hidden" name="copy" id="a-copy" value="Copy">
    <a href="javascript:document.getElementById('a-copy').click();" class="group-btn"><i class="fa fa-files-o"></i> Copy </a>
    <!-- <a href="https://github.com/prasathmani/tinyfilemanager" target="_blank" class="float-right" style="color:silver">Tiny File Manager</a></p> -->
    <?php endif;?>
</form>

<?php
fm_show_footer();

//--- END

// Functions

/**
 * Delete  file or folder (recursively)
 * @param string $path
 * @return bool
 */
function fm_rdelete($path)
{
    if (is_link($path)) {
        return unlink($path);
    } elseif (is_dir($path)) {
        $objects = scandir($path);
        $ok      = true;
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rdelete($path . '/' . $file)) {
                        $ok = false;
                    }
                }
            }
        }
        return ($ok) ? rmdir($path) : false;
    } elseif (is_file($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Recursive chmod
 * @param string $path
 * @param int $filemode
 * @param int $dirmode
 * @return bool
 * @todo Will use in mass chmod
 */
function fm_rchmod($path, $filemode, $dirmode)
{
    if (is_dir($path)) {
        if (!chmod($path, $dirmode)) {
            return false;
        }
        $objects = scandir($path);
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rchmod($path . '/' . $file, $filemode, $dirmode)) {
                        return false;
                    }
                }
            }
        }
        return true;
    } elseif (is_link($path)) {
        return true;
    } elseif (is_file($path)) {
        return chmod($path, $filemode);
    }
    return false;
}

/**
 * Safely rename
 * @param string $old
 * @param string $new
 * @return bool|null
 */
function fm_rename($old, $new)
{
    return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
}

/**
 * Copy file or folder (recursively).
 * @param string $path
 * @param string $dest
 * @param bool $upd Update files
 * @param bool $force Create folder with same names instead file
 * @return bool
 */
function fm_rcopy($path, $dest, $upd = true, $force = true)
{
    if (is_dir($path)) {
        if (!fm_mkdir($dest, $force)) {
            return false;
        }
        $objects = scandir($path);
        $ok      = true;
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (!fm_rcopy($path . '/' . $file, $dest . '/' . $file)) {
                        $ok = false;
                    }
                }
            }
        }
        return $ok;
    } elseif (is_file($path)) {
        return fm_copy($path, $dest, $upd);
    }
    return false;
}

/**
 * Safely create folder
 * @param string $dir
 * @param bool $force
 * @return bool
 */
function fm_mkdir($dir, $force)
{
    if (file_exists($dir)) {
        if (is_dir($dir)) {
            return $dir;
        } elseif (!$force) {
            return false;
        }
        unlink($dir);
    }
    return mkdir($dir, 0777, true);
}

/**
 * Safely copy file
 * @param string $f1
 * @param string $f2
 * @param bool $upd
 * @return bool
 */
function fm_copy($f1, $f2, $upd)
{
    $time1 = filemtime($f1);
    if (file_exists($f2)) {
        $time2 = filemtime($f2);
        if ($time2 >= $time1 && $upd) {
            return false;
        }
    }
    $ok = copy($f1, $f2);
    if ($ok) {
        touch($f2, $time1);
    }
    return $ok;
}

/**
 * Get mime type
 * @param string $file_path
 * @return mixed|string
 */
function fm_get_mime_type($file_path)
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime;
    } elseif (function_exists('mime_content_type')) {
        return mime_content_type($file_path);
    } elseif (!stristr(ini_get('disable_functions'), 'shell_exec')) {
        $file = escapeshellarg($file_path);
        $mime = shell_exec('file -bi ' . $file);
        return $mime;
    } else {
        return '--';
    }
}

/**
 * HTTP Redirect
 * @param string $url
 * @param int $code
 */
function fm_redirect($url, $code = 302)
{
    header('Location: ' . $url, true, $code);
    exit;
}

/**
 * Clean path
 * @param string $path
 * @return string
 */
function fm_clean_path($path)
{
    $path = trim($path);
    $path = trim($path, '\\/');
    $path = str_replace(array(
        '../',
        '..\\'
    ), '', $path);
    if ($path == '..') {
        $path = '';
    }
    return str_replace('\\', '/', $path);
}

/**
 * Get parent path
 * @param string $path
 * @return bool|string
 */
function fm_get_parent_path($path)
{
    $path = fm_clean_path($path);
    if ($path != '') {
        $array = explode('/', $path);
        if (count($array) > 1) {
            $array = array_slice($array, 0, -1);
            return implode('/', $array);
        }
        return '';
    }
    return false;
}

/**
 * Get nice filesize
 * @param int $size
 * @return string
 */
function fm_get_filesize($size)
{
    if ($size < 1000) {
        return sprintf('%s B', $size);
    } elseif (($size / 1024) < 1000) {
        return sprintf('%s KiB', round(($size / 1024), 2));
    } elseif (($size / 1024 / 1024) < 1000) {
        return sprintf('%s MiB', round(($size / 1024 / 1024), 2));
    } elseif (($size / 1024 / 1024 / 1024) < 1000) {
        return sprintf('%s GiB', round(($size / 1024 / 1024 / 1024), 2));
    } else {
        return sprintf('%s TiB', round(($size / 1024 / 1024 / 1024 / 1024), 2));
    }
}

/**
 * Get info about zip archive
 * @param string $path
 * @return array|bool
 */
function fm_get_zif_info($path)
{
    if (function_exists('zip_open')) {
        $arch = zip_open($path);
        if ($arch) {
            $filenames = array();
            while ($zip_entry = zip_read($arch)) {
                $zip_name    = zip_entry_name($zip_entry);
                $zip_folder  = substr($zip_name, -1) == '/';
                $filenames[] = array(
                    'name' => $zip_name,
                    'filesize' => zip_entry_filesize($zip_entry),
                    'compressed_size' => zip_entry_compressedsize($zip_entry),
                    'folder' => $zip_folder
                    //'compression_method' => zip_entry_compressionmethod($zip_entry),
                );
            }
            zip_close($arch);
            return $filenames;
        }
    }
    return false;
}

/**
 * Encode html entities
 * @param string $text
 * @return string
 */
function fm_enc($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * This function scans the files folder recursively, and builds a large array
 * @param string $dir
 * @return json
 */
function scan($dir)
{
    $files = array();
    $_dir  = $dir;
    $dir   = FM_ROOT_PATH . '/' . $dir;
    // Is there actually such a folder/file?
    if (file_exists($dir)) {
        foreach (scandir($dir) as $f) {
            if (!$f || $f[0] == '.') {
                continue; // Ignore hidden files
            }
            
            if (is_dir($dir . '/' . $f)) {
                // The path is a folder
                $files[] = array(
                    "name" => $f,
                    "type" => "folder",
                    "path" => $_dir . '/' . $f,
                    "items" => scan($dir . '/' . $f) // Recursively get the contents of the folder
                );
            } else {
                // It is a file
                $files[] = array(
                    "name" => $f,
                    "type" => "file",
                    "path" => $_dir,
                    "size" => filesize($dir . '/' . $f) // Gets the size of this file
                );
            }
        }
    }
    return $files;
}

/**
 * Scan directory and return tree view
 * @param string $directory
 * @param boolean $first_call
 */
function php_file_tree_dir($directory, $first_call = true)
{
    // Recursive function called by php_file_tree() to list directories/files
    
    $php_file_tree = "";
    // Get and sort directories/files
    if (function_exists("scandir"))
        $file = scandir($directory);
    natcasesort($file);
    // Make directories first
    $files = $dirs = array();
    foreach ($file as $this_file) {
        if (is_dir("$directory/$this_file")) {
            if (!in_array($this_file, $GLOBALS['exclude_folders'])) {
                $dirs[] = $this_file;
            }
        } else {
            $files[] = $this_file;
        }
    }
    $file = array_merge($dirs, $files);
    
    if (count($file) > 2) { // Use 2 instead of 0 to account for . and .. "directories"
        $php_file_tree = "<ul";
        if ($first_call) {
            $php_file_tree .= " class=\"php-file-tree\"";
            $first_call = false;
        }
        $php_file_tree .= ">";
        foreach ($file as $this_file) {
            if ($this_file != "." && $this_file != "..") {
                if (is_dir("$directory/$this_file")) {
                    // Directory
                    $php_file_tree .= "<li class=\"pft-directory\"><i class=\"fa fa-folder-o\"></i><a href=\"#\">" . htmlspecialchars($this_file) . "</a>";
                    $php_file_tree .= php_file_tree_dir("$directory/$this_file", false);
                    $php_file_tree .= "</li>";
                } else {
                    // File
                    $ext  = fm_get_file_icon_class($this_file);
                    $path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $directory);
                    $link = "?p=" . "$path" . "&view=" . urlencode($this_file);
                    $php_file_tree .= "<li class=\"pft-file\"><a href=\"$link\"> <i class=\"$ext\"></i>" . htmlspecialchars($this_file) . "</a></li>";
                }
            }
        }
        $php_file_tree .= "</ul>";
    }
    return $php_file_tree;
}

/**
 * Scan directory and render tree view
 * @param string $directory
 */
function php_file_tree($directory)
{
    // Remove trailing slash
    $code = "";
    if (substr($directory, -1) == "/")
        $directory = substr($directory, 0, strlen($directory) - 1);
    if (function_exists('php_file_tree_dir')) {
        $code .= php_file_tree_dir($directory);
        return $code;
    }
}

/**
 * Save message in session
 * @param string $msg
 * @param string $status
 */
function fm_set_msg($msg, $status = 'ok')
{
    $_SESSION['message'] = $msg;
    $_SESSION['status']  = $status;
}

/**
 * Check if string is in UTF-8
 * @param string $string
 * @return int
 */
function fm_is_utf8($string)
{
    return preg_match('//u', $string);
}

/**
 * Convert file name to UTF-8 in Windows
 * @param string $filename
 * @return string
 */
function fm_convert_win($filename)
{
    if (FM_IS_WIN && function_exists('iconv')) {
        $filename = iconv(FM_ICONV_INPUT_ENC, 'UTF-8//IGNORE', $filename);
    }
    return $filename;
}

/**
 * Get CSS classname for file
 * @param string $path
 * @return string
 */
function fm_get_file_icon_class($path)
{
    // get extension
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    switch ($ext) {
        case 'ico':
        case 'gif':
        case 'jpg':
        case 'jpeg':
        case 'jpc':
        case 'jp2':
        case 'jpx':
        case 'xbm':
        case 'wbmp':
        case 'png':
        case 'bmp':
        case 'tif':
        case 'tiff':
        case 'svg':
            $img = 'fa fa-picture-o';
            break;
        case 'passwd':
        case 'ftpquota':
        case 'sql':
        case 'js':
        case 'json':
        case 'sh':
        case 'config':
        case 'twig':
        case 'tpl':
        case 'md':
        case 'gitignore':
        case 'c':
        case 'cpp':
        case 'cs':
        case 'py':
        case 'map':
        case 'lock':
        case 'dtd':
            $img = 'fa fa-file-code-o';
            break;
        case 'txt':
        case 'ini':
        case 'conf':
        case 'log':
        case 'htaccess':
            $img = 'fa fa-file-text-o';
            break;
        case 'css':
        case 'less':
        case 'sass':
        case 'scss':
            $img = 'fa fa-css3';
            break;
        case 'zip':
        case 'rar':
        case 'gz':
        case 'tar':
        case '7z':
            $img = 'fa fa-file-archive-o';
            break;
        case 'php':
        case 'php4':
        case 'php5':
        case 'phps':
        case 'phtml':
            $img = 'fa fa-code';
            break;
        case 'htm':
        case 'html':
        case 'shtml':
        case 'xhtml':
            $img = 'fa fa-html5';
            break;
        case 'xml':
        case 'xsl':
            $img = 'fa fa-file-excel-o';
            break;
        case 'wav':
        case 'mp3':
        case 'mp2':
        case 'm4a':
        case 'aac':
        case 'ogg':
        case 'oga':
        case 'wma':
        case 'mka':
        case 'flac':
        case 'ac3':
        case 'tds':
            $img = 'fa fa-music';
            break;
        case 'm3u':
        case 'm3u8':
        case 'pls':
        case 'cue':
            $img = 'fa fa-headphones';
            break;
        case 'avi':
        case 'mpg':
        case 'mpeg':
        case 'mp4':
        case 'm4v':
        case 'flv':
        case 'f4v':
        case 'ogm':
        case 'ogv':
        case 'mov':
        case 'mkv':
        case '3gp':
        case 'asf':
        case 'wmv':
            $img = 'fa fa-file-video-o';
            break;
        case 'eml':
        case 'msg':
            $img = 'fa fa-envelope-o';
            break;
        case 'xls':
        case 'xlsx':
            $img = 'fa fa-file-excel-o';
            break;
        case 'csv':
            $img = 'fa fa-file-text-o';
            break;
        case 'bak':
            $img = 'fa fa-clipboard';
            break;
        case 'doc':
        case 'docx':
            $img = 'fa fa-file-word-o';
            break;
        case 'ppt':
        case 'pptx':
            $img = 'fa fa-file-powerpoint-o';
            break;
        case 'ttf':
        case 'ttc':
        case 'otf':
        case 'woff':
        case 'woff2':
        case 'eot':
        case 'fon':
            $img = 'fa fa-font';
            break;
        case 'pdf':
            $img = 'fa fa-file-pdf-o';
            break;
        case 'psd':
        case 'ai':
        case 'eps':
        case 'fla':
        case 'swf':
            $img = 'fa fa-file-image-o';
            break;
        case 'exe':
        case 'msi':
            $img = 'fa fa-file-o';
            break;
        case 'bat':
            $img = 'fa fa-terminal';
            break;
        default:
            $img = 'fa fa-info-circle';
    }
    
    return $img;
}

/**
 * Get image files extensions
 * @return array
 */
function fm_get_image_exts()
{
    return array('ico', 'gif', 'jpg', 'jpeg', 'jpc', 'jp2', 'jpx', 'xbm', 'wbmp', 'png', 'bmp', 'tif', 'tiff', 'psd');
}

/**
 * Get video files extensions
 * @return array
 */
function fm_get_video_exts()
{
    return array('webm', 'mp4', 'm4v', 'ogm', 'ogv', 'mov');
}

/**
 * Get audio files extensions
 * @return array
 */
function fm_get_audio_exts()
{
    return array('wav', 'mp3', 'ogg', 'm4a');
}

/**
 * Get text file extensions
 * @return array
 */
function fm_get_text_exts()
{
    return array(
        'txt', 'css', 'ini', 'conf', 'log', 'htaccess', 'passwd', 'ftpquota', 'sql', 'js', 'json', 'sh', 'config',
        'php', 'php4', 'php5', 'phps', 'phtml', 'htm', 'html', 'shtml', 'xhtml', 'xml', 'xsl', 'm3u', 'm3u8', 'pls', 'cue',
        'eml', 'msg', 'csv', 'bat', 'twig', 'tpl', 'md', 'gitignore', 'less', 'sass', 'scss', 'c', 'cpp', 'cs', 'py',
        'map', 'lock', 'dtd', 'svg',
    );
}

/**
 * Get mime types of text files
 * @return array
 */
function fm_get_text_mimes()
{
    return array(
        'application/xml',
        'application/javascript',
        'application/x-javascript',
        'image/svg+xml',
        'message/rfc822'
    );
}

/**
 * Get file names of text files w/o extensions
 * @return array
 */
function fm_get_text_names()
{
    return array(
        'license',
        'readme',
        'authors',
        'contributors',
        'changelog'
    );
}

/**
 * Class to work with zip files (using ZipArchive)
 */
class FM_Zipper
{
    private $zip;
    
    public function __construct()
    {
        $this->zip = new ZipArchive();
    }
    
    /**
     * Create archive with name $filename and files $files (RELATIVE PATHS!)
     * @param string $filename
     * @param array|string $files
     * @return bool
     */
    public function create($filename, $files)
    {
        $res = $this->zip->open($filename, ZipArchive::CREATE);
        if ($res !== true) {
            return false;
        }
        if (is_array($files)) {
            foreach ($files as $f) {
                if (!$this->addFileOrDir($f)) {
                    $this->zip->close();
                    return false;
                }
            }
            $this->zip->close();
            return true;
        } else {
            if ($this->addFileOrDir($files)) {
                $this->zip->close();
                return true;
            }
            return false;
        }
    }
    
    /**
     * Extract archive $filename to folder $path (RELATIVE OR ABSOLUTE PATHS)
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function unzip($filename, $path)
    {
        $res = $this->zip->open($filename);
        if ($res !== true) {
            return false;
        }
        if ($this->zip->extractTo($path)) {
            $this->zip->close();
            return true;
        }
        return false;
    }
    
    /**
     * Add file/folder to archive
     * @param string $filename
     * @return bool
     */
    private function addFileOrDir($filename)
    {
        if (is_file($filename)) {
            return $this->zip->addFile($filename);
        } elseif (is_dir($filename)) {
            return $this->addDir($filename);
        }
        return false;
    }
    
    /**
     * Add folder recursively
     * @param string $path
     * @return bool
     */
    private function addDir($path)
    {
        if (!$this->zip->addEmptyDir($path)) {
            return false;
        }
        $objects = scandir($path);
        if (is_array($objects)) {
            foreach ($objects as $file) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($path . '/' . $file)) {
                        if (!$this->addDir($path . '/' . $file)) {
                            return false;
                        }
                    } elseif (is_file($path . '/' . $file)) {
                        if (!$this->zip->addFile($path . '/' . $file)) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}

//--- templates functions

/**
 * Show nav block
 * @param string $path
 */
function fm_show_nav_path($path)
{
    global $lang;
?>
    <div class="path main-nav">
<?php
    $path     = fm_clean_path($path);
    $root_url = "<a href='?p='><i class='fa fa-home' aria-hidden='true' title='" . FM_ROOT_PATH . "'></i></a>";
    $sep      = '<i class="fa fa-caret-right"></i>';
    if ($path != '') {
        $exploded = explode('/', $path);
        $count    = count($exploded);
        $array    = array();
        $parent   = '';
        for ($i = 0; $i < $count; $i++) {
            $parent     = trim($parent . '/' . $exploded[$i], '/');
            $parent_enc = urlencode($parent);
            $array[]    = "<a href='?p={$parent_enc}'>" . fm_enc(fm_convert_win($exploded[$i])) . "</a>";
        }
        $root_url .= $sep . implode($sep, $array);
    }
    echo '<div class="break-word float-left">' . $root_url . '</div>';
?>
        <div class="float-right">
        <?php if (!FM_READONLY): ?>
            <a title="Search" href="javascript:showSearch('<?php echo urlencode(FM_PATH);?>')"><i class="fa fa-search"></i></a>
            <a title="Upload files" href="?p=<?php echo urlencode(FM_PATH); ?>&amp;upload"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
            <a title="New folder" href="#createNewItem" ><i class="fa fa-plus-square"></i></a>
        <?php endif; ?>
        <?php if (FM_USE_AUTH):?><a title="Logout" href="?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i></a><?php endif;?>
        </div>
</div>
<?php
}

/**
 * Show message from session
 */
function fm_show_message()
{
    if (isset($_SESSION['message'])) {
        $class = isset($_SESSION['status']) ? $_SESSION['status'] : 'ok';
        echo '<p class="message ' . $class . '">' . $_SESSION['message'] . '</p>';
        unset($_SESSION['message']);
        unset($_SESSION['status']);
    }
}

/**
 * Show page header in Login Form
 */
function fm_show_header_login()
{
    $sprites_ver = '20160315';
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    
    global $lang;
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>File Manager</title>
        <meta charset="utf-8">
        <meta name="Description" CONTENT="Author: CCP Programmers, Tiny PHP File Manager">
        <meta name="robots" content="noindex,nofollow" />
        <meta name="googlebot" content="noindex" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="renderer" content="webkit" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0" />
        <link rel="icon" href="<?php echo FM_SELF_URL;?>?img=favicon" type="image/png">
        <link rel="shortcut icon" href="<?php echo FM_SELF_URL;?>?img=favicon" type="image/png"/>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        <style type="text/css">a img,img{border:none}.filename,td,th{white-space:nowrap}.close,.close:focus,.close:hover,.php-file-tree a,a{text-decoration:none}a,body,code,div,em,form,html,img,label,li,ol,p,pre,small,span,strong,table,td,th,tr,ul{margin:0;padding:0;vertical-align:baseline;outline:0;font-size:100%;background:0 0;border:none;text-decoration:none}p,table,ul{margin-bottom:10px}html{overflow-y:scroll}body{padding:0;font:13px/16px Tahoma,Arial,sans-serif;color:#222;background:#F7F7F7;margin:50px 30px 0}button,input,select,textarea{font-size:inherit;font-family:inherit}a{color:#296ea3}a:hover{color:#b00}img{vertical-align:middle}span{color:#777}small{font-size:11px;color:#999}ul{list-style-type:none;margin-left:0}ul li{padding:3px 0}table{border-collapse:collapse;border-spacing:0;width:100%}.file-tree-view+#main-table{width:75%!important;float:left}td,th{padding:4px 7px;text-align:left;vertical-align:top;border:1px solid #ddd;background:#fff}td.gray,th{background-color:#eee}td.gray span{color:#222}tr:hover td{background-color:#f5f5f5}tr:hover td.gray{background-color:#eee}.table{width:100%;max-width:100%;margin-bottom:1rem}.table td,.table th{padding:.55rem;vertical-align:top;border-top:1px solid #ddd}.table thead th{vertical-align:bottom;border-bottom:2px solid #eceeef}.table tbody+tbody{border-top:2px solid #eceeef}.table .table{background-color:#fff}code,pre{display:block;margin-bottom:10px;font:13px/16px Consolas,'Courier New',Courier,monospace;border:1px dashed #ccc;padding:5px;overflow:auto}.hidden,.modal{display:none}.btn,.close{font-weight:700}pre.with-hljs{padding:0}pre.with-hljs code{margin:0;border:0;overflow:visible}code.maxheight,pre.maxheight{max-height:512px}input[type=checkbox]{margin:0;padding:0}.message,.path{padding:4px 7px;border:1px solid #ddd;background-color:#fff}.fa.fa-caret-right{font-size:1.2em;margin:0 4px;vertical-align:middle;color:#ececec}.fa.fa-home{font-size:1.2em;vertical-align:bottom}#wrapper{min-width:400px;margin:0 auto}.path{margin-bottom:10px}.right{text-align:right}.center,.close,.login-form{text-align:center}.float-right{float:right}.float-left{float:left}.message.ok{border-color:green;color:green}.message.error{border-color:red;color:red}.message.alert{border-color:orange;color:orange}.btn{border:0;background:0 0;padding:0;margin:0;color:#296ea3;cursor:pointer}.btn:hover{color:#b00}.preview-img{max-width:100%;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAKklEQVR42mL5//8/Azbw+PFjrOJMDCSCUQ3EABZc4S0rKzsaSvTTABBgAMyfCMsY4B9iAAAAAElFTkSuQmCC)}.inline-actions>a>i{font-size:1em;margin-left:5px;background:#3785c1;color:#fff;padding:3px;border-radius:3px}.preview-video{position:relative;max-width:100%;height:0;padding-bottom:62.5%;margin-bottom:10px}.preview-video video{position:absolute;width:100%;height:100%;left:0;top:0;background:#000}.compact-table{border:0;width:auto}.compact-table td,.compact-table th{width:100px;border:0;text-align:center}.compact-table tr:hover td{background-color:#fff}.filename{max-width:420px;overflow:hidden;text-overflow:ellipsis}.break-word{word-wrap:break-word;margin-left:30px}.break-word.float-left a{color:#7d7d7d}.break-word+.float-right{padding-right:30px;position:relative}.break-word+.float-right>a{color:#7d7d7d;font-size:1.2em;margin-right:4px}.modal{position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}#editor,.edit-file-actions{position:absolute;right:30px}.modal-content{background-color:#fefefe;margin:auto;padding:20px;border:1px solid #888;width:80%}.close:focus,.close:hover{color:#000;cursor:pointer}#editor{top:50px;bottom:5px;left:30px}.edit-file-actions{top:0;background:#fff;margin-top:5px}.edit-file-actions>a,.edit-file-actions>button{background:#fff;padding:5px 15px;cursor:pointer;color:#296ea3;border:1px solid #296ea3}.group-btn{background:#fff;padding:2px 6px;border:1px solid;cursor:pointer;color:#296ea3}.main-nav{position:fixed;top:0;left:0;padding:10px 30px 10px 1px;width:100%;background:#fff;color:#000;border:0;box-shadow:0 4px 5px 0 rgba(0,0,0,.14),0 1px 10px 0 rgba(0,0,0,.12),0 2px 4px -1px rgba(0,0,0,.2)}.login-form{width:320px;margin:0 auto;box-shadow:0 8px 10px 1px rgba(0,0,0,.14),0 3px 14px 2px rgba(0,0,0,.12),0 5px 5px -3px rgba(0,0,0,.2)}.login-form label,.path.login-form input{padding:8px;margin:10px}.footer-links{background:0 0;border:0;clear:both}select[name=lang]{border:none;position:relative;text-transform:uppercase;left:-30%;top:12px;color:silver}input[type=search]{height:30px;margin:5px;width:80%;border:1px solid #ccc}.path.login-form input[type=submit]{background-color:#4285f4;color:#fff;border:1px solid;border-radius:2px;font-weight:700;cursor:pointer}.modalDialog{position:fixed;font-family:Arial,Helvetica,sans-serif;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.8);z-index:99999;opacity:0;-webkit-transition:opacity .4s ease-in;-moz-transition:opacity .4s ease-in;transition:opacity .4s ease-in;pointer-events:none}.modalDialog:target{opacity:1;pointer-events:auto}.modalDialog>.model-wrapper{max-width:400px;position:relative;margin:10% auto;padding:15px;border-radius:2px;background:#fff}.close{float:right;background:#fff;color:#000;line-height:25px;position:absolute;right:0;top:0;width:24px;border-radius:0 5px 0 0;font-size:18px}.close:hover{background:#e4e4e4}.modalDialog p{line-height:30px}div#searchresultWrapper{max-height:320px;overflow:auto}div#searchresultWrapper li{margin:8px 0;list-style:none}li.file:before,li.folder:before{font:normal normal normal 14px/1 FontAwesome;content:"\f016";margin-right:5px}li.folder:before{content:"\f114"}i.fa.fa-folder-o{color:#eeaf4b}i.fa.fa-picture-o{color:#26b99a}i.fa.fa-file-archive-o{color:#da7d7d}.footer-links i.fa.fa-file-archive-o{color:#296ea3}i.fa.fa-css3{color:#f36fa0}i.fa.fa-file-code-o{color:#ec6630}i.fa.fa-code{color:#cc4b4c}i.fa.fa-file-text-o{color:#0096e6}i.fa.fa-html5{color:#d75e72}i.fa.fa-file-excel-o{color:#09c55d}i.fa.fa-file-powerpoint-o{color:#f6712e}.file-tree-view{width:24%;float:left;overflow:auto;border:1px solid #ddd;border-right:0;background:#fff}.file-tree-view .tree-title{background:#eee;padding:9px 2px 9px 10px;font-weight:700}.file-tree-view ul{margin-left:15px;margin-bottom:0}.file-tree-view i{padding-right:3px}.php-file-tree{font-size:100%;letter-spacing:1px;line-height:1.5;margin-left:5px!important}.php-file-tree a{color:#296ea3}.php-file-tree A:hover{color:#b00}.php-file-tree .open{font-style:italic;color:#2183ce}.php-file-tree .closed{font-style:normal}#file-tree-view::-webkit-scrollbar{width:10px;background-color:#F5F5F5}#file-tree-view::-webkit-scrollbar-track{border-radius:10px;background:rgba(0,0,0,.1);border:1px solid #ccc}#file-tree-view::-webkit-scrollbar-thumb{border-radius:10px;background:linear-gradient(left,#fff,#e4e4e4);border:1px solid #aaa}#file-tree-view::-webkit-scrollbar-thumb:hover{background:#fff}#file-tree-view::-webkit-scrollbar-thumb:active{background:linear-gradient(left,#22ADD4,#1E98BA)}.dropzone.dz-message{margin:3.3em 0}</style>
    </head>
    <body>
        <div id="wrapper">
<?php
}

/**
 * Show page footer in Login Form
 */
function fm_show_footer_login()
{
?>
</div>
</body>
</html>
<?php
}

/**
 * Show page header
 */
function fm_show_header()
{
    $sprites_ver = '20160315';
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    
    global $lang;
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>File Manager</title>
        <meta charset="utf-8">
        <meta name="Description" CONTENT="Author: CCP Programmers, Tiny PHP File Manager">
        <meta name="robots" content="noindex,nofollow" />
        <meta name="googlebot" content="noindex" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="renderer" content="webkit" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0" />
        <link rel="icon" href="<?php echo FM_SELF_URL;?>?img=favicon" type="image/png">
        <link rel="shortcut icon" href="<?php echo FM_SELF_URL;?>?img=favicon" type="image/png">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        <?php if (isset($_GET['view']) && FM_USE_HIGHLIGHTJS):?>
            <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/<?php echo FM_HIGHLIGHTJS_STYLE;?>.min.css">
        <?php endif;?>
        <style type="text/css">a img,img{border:none}.filename,td,th{white-space:nowrap}.close,.close:focus,.close:hover,.php-file-tree a,a{text-decoration:none}a,body,code,div,em,form,html,img,label,li,ol,p,pre,small,span,strong,table,td,th,tr,ul{margin:0;padding:0;vertical-align:baseline;outline:0;font-size:100%;background:0 0;border:none;text-decoration:none}p,table,ul{margin-bottom:10px}html{overflow-y:scroll}body{padding:0;font:13px/16px Tahoma,Arial,sans-serif;color:#222;background:#F7F7F7;margin:50px 30px 0}button,input,select,textarea{font-size:inherit;font-family:inherit}a{color:#296ea3}a:hover{color:#b00}img{vertical-align:middle}span{color:#777}small{font-size:11px;color:#999}ul{list-style-type:none;margin-left:0}ul li{padding:3px 0}table{border-collapse:collapse;border-spacing:0;width:100%}.file-tree-view+#main-table{width:75%!important;float:left}td,th{padding:4px 7px;text-align:left;vertical-align:top;border:1px solid #ddd;background:#fff}td.gray,th{background-color:#eee}td.gray span{color:#222}tr:hover td{background-color:#f5f5f5}tr:hover td.gray{background-color:#eee}.table{width:100%;max-width:100%;margin-bottom:1rem}.table td,.table th{padding:.55rem;vertical-align:top;border-top:1px solid #ddd}.table thead th{vertical-align:bottom;border-bottom:2px solid #eceeef}.table tbody+tbody{border-top:2px solid #eceeef}.table .table{background-color:#fff}code,pre{display:block;margin-bottom:10px;font:13px/16px Consolas,'Courier New',Courier,monospace;border:1px dashed #ccc;padding:5px;overflow:auto}.hidden,.modal{display:none}.btn,.close{font-weight:700}pre.with-hljs{padding:0}pre.with-hljs code{margin:0;border:0;overflow:visible}code.maxheight,pre.maxheight{max-height:512px}input[type=checkbox]{margin:0;padding:0}.message,.path{padding:4px 7px;border:1px solid #ddd;background-color:#fff}.fa.fa-caret-right{font-size:1.2em;margin:0 4px;vertical-align:middle;color:#ececec}.fa.fa-home{font-size:1.2em;vertical-align:bottom}#wrapper{min-width:400px;margin:0 auto}.path{margin-bottom:10px}.right{text-align:right}.center,.close,.login-form{text-align:center}.float-right{float:right}.float-left{float:left}.message.ok{border-color:green;color:green}.message.error{border-color:red;color:red}.message.alert{border-color:orange;color:orange}.btn{border:0;background:0 0;padding:0;margin:0;color:#296ea3;cursor:pointer}.btn:hover{color:#b00}.preview-img{max-width:100%;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAKklEQVR42mL5//8/Azbw+PFjrOJMDCSCUQ3EABZc4S0rKzsaSvTTABBgAMyfCMsY4B9iAAAAAElFTkSuQmCC)}.inline-actions>a>i{font-size:1em;margin-left:5px;background:#3785c1;color:#fff;padding:3px;border-radius:3px}.preview-video{position:relative;max-width:100%;height:0;padding-bottom:62.5%;margin-bottom:10px}.preview-video video{position:absolute;width:100%;height:100%;left:0;top:0;background:#000}.compact-table{border:0;width:auto}.compact-table td,.compact-table th{width:100px;border:0;text-align:center}.compact-table tr:hover td{background-color:#fff}.filename{max-width:420px;overflow:hidden;text-overflow:ellipsis}.break-word{word-wrap:break-word;margin-left:30px}.break-word.float-left a{color:#7d7d7d}.break-word+.float-right{padding-right:30px;position:relative}.break-word+.float-right>a{color:#7d7d7d;font-size:1.2em;margin-right:4px}.modal{position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}#editor,.edit-file-actions{position:absolute;right:30px}.modal-content{background-color:#fefefe;margin:auto;padding:20px;border:1px solid #888;width:80%}.close:focus,.close:hover{color:#000;cursor:pointer}#editor{top:50px;bottom:5px;left:30px}.edit-file-actions{top:0;background:#fff;margin-top:5px}.edit-file-actions>a,.edit-file-actions>button{background:#fff;padding:5px 15px;cursor:pointer;color:#296ea3;border:1px solid #296ea3}.group-btn{background:#fff;padding:2px 6px;border:1px solid;cursor:pointer;color:#296ea3}.main-nav{position:fixed;top:0;left:0;padding:10px 30px 10px 1px;width:100%;background:#fff;color:#000;border:0;box-shadow:0 4px 5px 0 rgba(0,0,0,.14),0 1px 10px 0 rgba(0,0,0,.12),0 2px 4px -1px rgba(0,0,0,.2)}.login-form{width:320px;margin:0 auto;box-shadow:0 8px 10px 1px rgba(0,0,0,.14),0 3px 14px 2px rgba(0,0,0,.12),0 5px 5px -3px rgba(0,0,0,.2)}.login-form label,.path.login-form input{padding:8px;margin:10px}.footer-links{background:0 0;border:0;clear:both}select[name=lang]{border:none;position:relative;text-transform:uppercase;left:-30%;top:12px;color:silver}input[type=search]{height:30px;margin:5px;width:80%;border:1px solid #ccc}.path.login-form input[type=submit]{background-color:#4285f4;color:#fff;border:1px solid;border-radius:2px;font-weight:700;cursor:pointer}.modalDialog{position:fixed;font-family:Arial,Helvetica,sans-serif;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.8);z-index:99999;opacity:0;-webkit-transition:opacity .4s ease-in;-moz-transition:opacity .4s ease-in;transition:opacity .4s ease-in;pointer-events:none}.modalDialog:target{opacity:1;pointer-events:auto}.modalDialog>.model-wrapper{max-width:400px;position:relative;margin:10% auto;padding:15px;border-radius:2px;background:#fff}.close{float:right;background:#fff;color:#000;line-height:25px;position:absolute;right:0;top:0;width:24px;border-radius:0 5px 0 0;font-size:18px}.close:hover{background:#e4e4e4}.modalDialog p{line-height:30px}div#searchresultWrapper{max-height:320px;overflow:auto}div#searchresultWrapper li{margin:8px 0;list-style:none}li.file:before,li.folder:before{font:normal normal normal 14px/1 FontAwesome;content:"\f016";margin-right:5px}li.folder:before{content:"\f114"}i.fa.fa-folder-o{color:#eeaf4b}i.fa.fa-picture-o{color:#26b99a}i.fa.fa-file-archive-o{color:#da7d7d}.footer-links i.fa.fa-file-archive-o{color:#296ea3}i.fa.fa-css3{color:#f36fa0}i.fa.fa-file-code-o{color:#ec6630}i.fa.fa-code{color:#cc4b4c}i.fa.fa-file-text-o{color:#0096e6}i.fa.fa-html5{color:#d75e72}i.fa.fa-file-excel-o{color:#09c55d}i.fa.fa-file-powerpoint-o{color:#f6712e}.file-tree-view{width:24%;float:left;overflow:auto;border:1px solid #ddd;border-right:0;background:#fff}.file-tree-view .tree-title{background:#eee;padding:9px 2px 9px 10px;font-weight:700}.file-tree-view ul{margin-left:15px;margin-bottom:0}.file-tree-view i{padding-right:3px}.php-file-tree{font-size:100%;letter-spacing:1px;line-height:1.5;margin-left:5px!important}.php-file-tree a{color:#296ea3}.php-file-tree A:hover{color:#b00}.php-file-tree .open{font-style:italic;color:#2183ce}.php-file-tree .closed{font-style:normal}#file-tree-view::-webkit-scrollbar{width:10px;background-color:#F5F5F5}#file-tree-view::-webkit-scrollbar-track{border-radius:10px;background:rgba(0,0,0,.1);border:1px solid #ccc}#file-tree-view::-webkit-scrollbar-thumb{border-radius:10px;background:linear-gradient(left,#fff,#e4e4e4);border:1px solid #aaa}#file-tree-view::-webkit-scrollbar-thumb:hover{background:#fff}#file-tree-view::-webkit-scrollbar-thumb:active{background:linear-gradient(left,#22ADD4,#1E98BA)}</style>
    </head>
    <body>
    <div id="wrapper">
        <div id="createNewItem" class="modalDialog"><div class="model-wrapper"><a href="#close" title="Close" class="close">X</a><h2>Create New Item</h2><p>
            <label for="newfile">Item Type &nbsp; : </label><input type="radio" name="newfile" id="newfile" value="file">File <input type="radio" name="newfile" value="folder" checked> Folder<br><label for="newfilename">Item Name : </label><input type="text" name="newfilename" id="newfilename" value=""><br>
            <input type="submit" name="submit" class="group-btn" value="Create Now" onclick="newfolder('<?php echo fm_enc(FM_PATH);?>');return false;"></p></div>
        </div>
        <div id="searchResult" class="modalDialog"><div class="model-wrapper"><a href="#close" title="Close" class="close">X</a>
            <input type="search" name="search" value="" placeholder="Find a item in current folder...">
            <h2>Search Results</h2>
            <div id="searchresultWrapper"></div>
        </div></div>
<?php
}

/**
 * Show page footer
 */
function fm_show_footer()
{
?>
</div>
<script>
function newfolder(e){var t=document.getElementById("newfilename").value,n=document.querySelector('input[name="newfile"]:checked').value;null!==t&&""!==t&&n&&(window.location.hash="#",window.location.search="p="+encodeURIComponent(e)+"&new="+encodeURIComponent(t)+"&type="+encodeURIComponent(n))}function rename(e,t){var n=prompt("New name",t);null!==n&&""!==n&&n!=t&&(window.location.search="p="+encodeURIComponent(e)+"&ren="+encodeURIComponent(t)+"&to="+encodeURIComponent(n))}function change_checkboxes(e,t){for(var n=e.length-1;n>=0;n--)e[n].checked="boolean"==typeof t?t:!e[n].checked}function get_checkboxes(){for(var e=document.getElementsByName("file[]"),t=[],n=e.length-1;n>=0;n--)(e[n].type="checkbox")&&t.push(e[n]);return t}function select_all(){change_checkboxes(get_checkboxes(),!0)}function unselect_all(){change_checkboxes(get_checkboxes(),!1)}function invert_all(){change_checkboxes(get_checkboxes())}function mailto(e,t){var n=new XMLHttpRequest,a="path="+e+"&file="+t+"&type=mail&ajax=true";n.open("POST","",!0),n.setRequestHeader("Content-type","application/x-www-form-urlencoded"),n.onreadystatechange=function(){4==n.readyState&&200==n.status&&alert(n.responseText)},n.send(a)}function showSearch(e){var t=new XMLHttpRequest,n="path="+e+"&type=search&ajax=true";t.open("POST","",!0),t.setRequestHeader("Content-type","application/x-www-form-urlencoded"),t.onreadystatechange=function(){4==t.readyState&&200==t.status&&(window.searchObj=t.responseText,document.getElementById("searchresultWrapper").innerHTML="",window.location.hash="#searchResult")},t.send(n)}function getSearchResult(e,t){var n=[],a=[];return e.forEach(function(e){"folder"===e.type?(getSearchResult(e.items,t),e.name.toLowerCase().match(t)&&n.push(e)):"file"===e.type&&e.name.toLowerCase().match(t)&&a.push(e)}),{folders:n,files:a}}function checkbox_toggle(){var e=get_checkboxes();e.push(this),change_checkboxes(e)}function backup(e,t){var n=new XMLHttpRequest,a="path="+e+"&file="+t+"&type=backup&ajax=true";return n.open("POST","",!0),n.setRequestHeader("Content-type","application/x-www-form-urlencoded"),n.onreadystatechange=function(){4==n.readyState&&200==n.status&&alert(n.responseText)},n.send(a),!1}function edit_save(e,t){var n="ace"==t?editor.getSession().getValue():document.getElementById("normal-editor").value;if(n){var a=document.createElement("form");a.setAttribute("method","POST"),a.setAttribute("action","");var o=document.createElement("textarea");o.setAttribute("type","textarea"),o.setAttribute("name","savedata");var c=document.createTextNode(n);o.appendChild(c),a.appendChild(o),document.body.appendChild(a),a.submit()}}function init_php_file_tree(){if(document.getElementsByTagName){for(var e=document.getElementsByTagName("LI"),t=0;t<e.length;t++){var n=e[t].className;if(n.indexOf("pft-directory")>-1)for(var a=e[t].childNodes,o=0;o<a.length;o++)"A"==a[o].tagName&&(a[o].onclick=function(){for(var e=this.nextSibling;;){if(null==e)return!1;if("UL"==e.tagName){var t="none"==e.style.display;return e.style.display=t?"block":"none",this.className=t?"open":"closed",!1}e=e.nextSibling}return!1},a[o].className=n.indexOf("open")>-1?"open":"closed"),"UL"==a[o].tagName&&(a[o].style.display=n.indexOf("open")>-1?"block":"none")}return!1}}var searchEl=document.querySelector("input[type=search]"),timeout=null;searchEl.onkeyup=function(e){clearTimeout(timeout);var t=JSON.parse(window.searchObj),n=document.querySelector("input[type=search]").value;timeout=setTimeout(function(){if(n.length>=2){var e=getSearchResult(t,n),a="",o="";e.folders.forEach(function(e){a+='<li class="'+e.type+'"><a href="?p='+e.path+'">'+e.name+"</a></li>"}),e.files.forEach(function(e){o+='<li class="'+e.type+'"><a href="?p='+e.path+"&view="+e.name+'">'+e.name+"</a></li>"}),document.getElementById("searchresultWrapper").innerHTML='<div class="model-wrapper">'+a+o+"</div>"}},500)},window.onload=init_php_file_tree;if(document.getElementById("file-tree-view")){var tableViewHt=document.getElementById("main-table").offsetHeight-2;document.getElementById("file-tree-view").setAttribute("style","height:"+tableViewHt+"px")};
</script>
<?php
    if (isset($_GET['view']) && FM_USE_HIGHLIGHTJS):
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<?php
    endif;
?>
<?php
    if (isset($_GET['edit']) && isset($_GET['env']) && FM_EDIT_FILE):
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js"></script>
<script>var editor = ace.edit("editor");editor.getSession().setMode("ace/mode/javascript");</script>
<?php
    endif;
?>
</body>
</html>
<?php
}

/**
 * Show image
 * @param string $img
 */
function fm_show_image($img)
{
    $modified_time = gmdate('D, d M Y 00:00:00') . ' GMT';
    $expires_time  = gmdate('D, d M Y 00:00:00', strtotime('+1 day')) . ' GMT';
    
    $img    = trim($img);
    $images = fm_get_images();
    $image  = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg==';
    if (isset($images[$img])) {
        $image = $images[$img];
    }
    $image = base64_decode($image);
    if (function_exists('mb_strlen')) {
        $size = mb_strlen($image, '8bit');
    } else {
        $size = strlen($image);
    }
    
    if (function_exists('header_remove')) {
        header_remove('Cache-Control');
        header_remove('Pragma');
    } else {
        header('Cache-Control:');
        header('Pragma:');
    }
    
    header('Last-Modified: ' . $modified_time, true, 200);
    header('Expires: ' . $expires_time);
    header('Content-Length: ' . $size);
    header('Content-Type: image/png');
    echo $image;
    
    exit;
}

/**
 * Get base64-encoded images
 * @return array
 */
function fm_get_images()
{
    return array(
        'favicon' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAlwSFlzAAALEwAACxMBAJqcGAAAAVlpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KTMInWQAAAx1JREFUOBElU8tuHEUUPbequsbzgLw8HiMBGqM4tgMmNquAsmEHiA8Iy0jZsAQpfAMLtmQTIRYREoIdSF7xWLDEyIFgIT9iSBAP45nM2OOZ7up6XO50WuquVvW955x7+hRtMmezm99/yP2D910xhsmeYq0tKaWhCPDJIx+PkIKHUpSU1jTj/ib/6O6tSx/8+RE9Pjx898y51u1v7n+GP4ab6UJ9RTWzNs4151GvtWELjzP+CCo5TjERtPWNNMriYPfQnj1/3TwcFN2TU8bGIyqHedf68pSD69Eov49n8wHeW38Onec7iCmRCGKAMqjMm/Zyx52MPjd7vePxcruFd+ZWNZIUcCQwYzLsYZ5/xcVOHcF72Zr2CoYsYJfFcpQyYzrmdf726myeYeT7nFJJkJkZBNVKqNUUypNjpOilWRqhQHpG3rW8JwqgaMzk9zeHkyioQTMywF6Qj4QgjGWepl1CXJdbVfvIj6S2BKmMmJM2buKAWDBTRrXVG7CdJZli6riaul41SaGoSEJs4A53Udz7WCjGgmxgvBOuokd4+iU0Zi9CN8+CvYAKow+hml0ZJeaT1FuY9iICtcGjbZCdgymLkrhwUDMRPhdUUxNFHj/+tIWd3T1orbF0aRHray+LAl/VeBeRcicOlDChFNPKEqoMFSOJ1Ciu7+wfYGFhAZdXlhFiQOkDFJPUCFHV48UXD6Nbltk2iRr6iWTnxE8BlPn3HzyAtQbdbhdRximlmaeJbBgxUoy1lg1TYmj2rFiF4HUqZSQBKPIck8kEp6fjas1klCjWUQgsUqLc8ovYmOEvD5We/GDxTAf1F2SmzHN0JfX7fVx77VWsr69hMBggF0CVWU7i2Xj3scG/3yHWr8Ic/VV8Gv/B2zXj/XyIs8k5m7znFy+vkDEavV4PQeQroqlY8c373lH4z/3GNTNXfEUbexu1g8W3Wq98eXepubj69UyjcX4yHpeSAysh4BijBHh6CjjIN+Py/Ph0594b29dv7F/b/uKkCqgUVtfW1s93JD83nXNycoRS4vVkmR4ACamdhoE+Wbty5Wa1IY//ATxUvT2+N/X3AAAAAElFTkSuQmCC',
        'logo' => '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',
        'logo1' => 'https://image.ibb.co/k92AFQ/h3k_logo_dark.png',
        'sprites' => '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
RK5CYII='
    );
}
