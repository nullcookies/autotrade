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
    'user' => '827ccb0eea8a706c4c34a16891f84e7b', //12345
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
$GLOBALS['exclude_folders'] = array(
);

// include user config php file
if (defined('FM_CONFIG') && is_file(FM_CONFIG) ) {
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

$is_https = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
    || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

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
                <img src="<?php echo FM_SELF_URL ?>?img=logo" alt="File manager" width="159" style="margin:20px;">
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
    if(isset($_POST['type']) && $_POST['type']=="search") {
        $dir = $_POST['path'];
        $response = scan($dir);
        echo json_encode($response);
    }

    //Send file to mail
    if (isset($_POST['type']) && $_POST['type']=="mail") {
        //send mail Fn removed.
    }

    //backup files
    if(isset($_POST['type']) && $_POST['type']=="backup") {
        $file = $_POST['file'];
        $path = $_POST['path'];
        $date = date("dMy-His");
        $newFile = $file.'-'.$date.'.bak';
        copy($path.'/'.$file, $path.'/'.$newFile) or die("Unable to backup");
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
    $new = strip_tags($_GET['new']);
    $type = $_GET['type'];
    $new = fm_clean_path($new);
    $new = str_replace('/', '', $new);
    if ($new != '' && $new != '..' && $new != '.') {
        $path = FM_ROOT_PATH;
        if (FM_PATH != '') {
            $path .= '/' . FM_PATH;
        }
        if($_GET['type']=="file") {
            if(!file_exists($path . '/' . $new)) {
                @fopen($path . '/' . $new, 'w') or die('Cannot open file:  '.$new);
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
    $copy_to = fm_clean_path($_POST['copy_to']);
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
    $move = isset($_POST['move']);
    // copy/move
    $errors = 0;
    $files = $_POST['file'];
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
    $old = $_GET['ren'];
    $old = fm_clean_path($old);
    $old = str_replace('/', '', $old);
    // new name
    $new = $_GET['to'];
    $new = fm_clean_path($new);
    $new = str_replace('/', '', $new);
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
    $dl = $_GET['dl'];
    $dl = fm_clean_path($dl);
    $dl = str_replace('/', '', $dl);
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
    $f = $_FILES;
    $path = FM_ROOT_PATH;
    if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
    }

    $errors = 0;
    $uploads = 0;
    $total = count($f['file']['name']);
    $allowed = (FM_EXTENSION) ? explode(',', FM_EXTENSION) : false;

    $filename = $f['file']['name'];
    $tmp_name = $f['file']['tmp_name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

    if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none' && $isFileAllowed) {
        if (move_uploaded_file($tmp_name, $path . '/' . $f['file']['name'])) {
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
    $files = $_POST['file'];
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
            $zipname = $one_file . '_' . date('ymd_His') . '.zip';
        } else {
            $zipname = 'archive_' . date('ymd_His') . '.zip';
        }

        $zipper = new FM_Zipper();
        $res = $zipper->create($zipname, $files);

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
        $res = $zipper->unzip($zip_path, $path);

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
$files = array();
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
        <p class="break-word">Destination folder: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?p='.fm_enc(FM_PATH) ?>" class="dropzone" id="fileuploader" enctype="multipart/form-data">
            <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
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
            <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
            <input type="hidden" name="finish" value="1">
            <?php
            foreach ($copy_files as $cf) {
                echo '<input type="hidden" name="file[]" value="' . fm_enc($cf) . '">' . PHP_EOL;
            }
            ?>
            <p class="break-word">Files: <b><?php echo implode('</b>, <b>', $copy_files) ?></b></p>
            <p class="break-word">Source folder: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?><br>
                <label for="inp_copy_to">Destination folder:</label>
                <?php echo FM_ROOT_PATH ?>/<input type="text" name="copy_to" id="inp_copy_to" value="<?php echo fm_enc(FM_PATH) ?>">
            </p>
            <p><label><input type="checkbox" name="move" value="1"> Move'</label></p>
            <p>
                <button type="submit" class="btn"><i class="fa fa-check-circle"></i> Copy </button> &nbsp;
                <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
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
            Source path: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . $copy)) ?><br>
            Destination folder: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?>
        </p>
        <p>
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode($copy) ?>&amp;finish=1"><i class="fa fa-check-circle"></i> Copy</a></b> &nbsp;
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode($copy) ?>&amp;finish=1&amp;move=1"><i class="fa fa-check-circle"></i> Move</a></b> &nbsp;
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
        </p>
        <p><i>Select folder</i></p>
        <ul class="folders break-word">
            <?php
            if ($parent !== false) {
                ?>
                <li><a href="?p=<?php echo urlencode($parent) ?>&amp;copy=<?php echo urlencode($copy) ?>"><i class="fa fa-chevron-circle-left"></i> ..</a></li>
            <?php
            }
            foreach ($folders as $f) {
                ?>
                <li><a href="?p=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>&amp;copy=<?php echo urlencode($copy) ?>"><i class="fa fa-folder-o"></i> <?php echo fm_convert_win($f) ?></a></li>
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

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize = filesize($file_path);

    $is_zip = false;
    $is_image = false;
    $is_audio = false;
    $is_video = false;
    $is_text = false;

    $view_title = 'File';
    $filenames = false; // for zip
    $content = ''; // for text

    if ($ext == 'zip') {
        $is_zip = true;
        $view_title = 'Archive';
        $filenames = fm_get_zif_info($file_path);
    } elseif (in_array($ext, fm_get_image_exts())) {
        $is_image = true;
        $view_title = 'Image';
    } elseif (in_array($ext, fm_get_audio_exts())) {
        $is_audio = true;
        $view_title = 'Audio';
    } elseif (in_array($ext, fm_get_video_exts())) {
        $is_video = true;
        $view_title = 'Video';
    } elseif (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }

    ?>
    <div class="path">
        <p class="break-word"><b><?php echo $view_title ?> "<?php echo fm_enc(fm_convert_win($file)) ?>"</b></p>
        <p class="break-word">
            Full path: <?php echo fm_enc(fm_convert_win($file_path)) ?><br>
            File size: <?php echo fm_get_filesize($filesize) ?><?php if ($filesize >= 1000): ?> (<?php echo sprintf('%s bytes', $filesize) ?>)<?php endif; ?><br>
           MIME-type: <?php echo $mime_type ?><br>
            <?php
            // ZIP info
            if ($is_zip && $filenames !== false) {
                $total_files = 0;
                $total_comp = 0;
                $total_uncomp = 0;
                foreach ($filenames as $fn) {
                    if (!$fn['folder']) {
                        $total_files++;
                    }
                    $total_comp += $fn['compressed_size'];
                    $total_uncomp += $fn['filesize'];
                }
                ?>
                Files in archive: <?php echo $total_files ?><br>
                Total size: <?php echo fm_get_filesize($total_uncomp) ?><br>
                Size in archive: <?php echo fm_get_filesize($total_comp) ?><br>
                Compression: <?php echo round(($total_comp / $total_uncomp) * 100) ?>%<br>
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
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;dl=<?php echo urlencode($file) ?>"><i class="fa fa-cloud-download"></i> Download</a></b> &nbsp;
            <b><a href="<?php echo fm_enc($file_url) ?>" target="_blank"><i class="fa fa-external-link-square"></i> Open</a></b> &nbsp;
            <?php
            // ZIP actions
            if (!FM_READONLY && $is_zip && $filenames !== false) {
                $zip_name = pathinfo($file_path, PATHINFO_FILENAME);
                ?>
                <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;unzip=<?php echo urlencode($file) ?>"><i class="fa fa-check-circle"></i> UnZip</a></b> &nbsp;
                <b><a href="?p=<?php echo urlencode(FM_PATH) ?>&amp;unzip=<?php echo urlencode($file) ?>&amp;tofolder=1" title="UnZip to <?php echo fm_enc($zip_name) ?>"><i class="fa fa-check-circle"></i>
                    UnZip to folder</a></b> &nbsp;
                <?php
            }
            if($is_text && !FM_READONLY) {
            ?>
            <b><a href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>" class="edit-file"><i class="fa fa-pencil-square"></i> Edit</a></b> &nbsp;
            <b><a href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>&env=ace" class="edit-file"><i class="fa fa-pencil-square"></i> Advanced Edit</a></b> &nbsp;
            <?php }
            if($send_mail && !FM_READONLY) {
            ?>
            <b><a href="javascript:mailto('<?php echo urlencode(trim(FM_ROOT_PATH.'/'.FM_PATH)) ?>','<?php echo urlencode($file) ?>')"><i class="fa fa-pencil-square"></i> Mail</a></b> &nbsp;
            <?php } ?>
            <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-chevron-circle-left"></i> Back</a></b>
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
            if (in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico'))) {
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
                    'svg' => 'xml',
                );
                $hljs_class = isset($hljs_classes[$ext]) ? 'lang-' . $hljs_classes[$ext] : 'lang-' . $ext;
                if (empty($ext) || in_array(strtolower($file), fm_get_text_names()) || preg_match('#\.min\.(css|js)$#i', $file)) {
                    $hljs_class = 'nohighlight';
                }
                $content = '<pre class="with-hljs"><code class="' . $hljs_class . '">' . fm_enc($content) . '</code></pre>';
            } elseif (in_array($ext, array('php', 'php4', 'php5', 'phtml', 'phps'))) {
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

    $file_url = FM_ROOT_URL . fm_convert_win((FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file);
    $file_path = $path . '/' . $file;

    //normal editer
    $isNormalEditor = true;
    if(isset($_GET['env'])) {
        if($_GET['env'] == "ace") {
            $isNormalEditor = false;
        }
    }

    //Save File
    if(isset($_POST['savedata'])) {
        $writedata = $_POST['savedata'];
        $fd=fopen($file_path,"w");
        @fwrite($fd, $writedata);
        fclose($fd);
        fm_set_msg('File Saved Successfully', 'alert');
    }

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = fm_get_mime_type($file_path);
    $filesize = filesize($file_path);
    $is_text = false;
    $content = ''; // for text

    if (in_array($ext, fm_get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, fm_get_text_mimes())) {
        $is_text = true;
        $content = file_get_contents($file_path);
    }

    ?>
    <div class="path">
        <div class="edit-file-actions">
            <a title="Cancel" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;view=<?php echo urlencode($file) ?>"><i class="fa fa-reply-all"></i> Cancel</a>
            <a title="Backup" href="javascript:backup('<?php echo urlencode($path) ?>','<?php echo urlencode($file) ?>')"><i class="fa fa-database"></i> Backup</a>
            <?php if($is_text) { ?>
                <?php if($isNormalEditor) { ?>
                    <a title="Advanced" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>&amp;env=ace"><i class="fa fa-paper-plane"></i> Advanced Editor</a>
                    <button type="button" name="Save" data-url="<?php echo fm_enc($file_url) ?>" onclick="edit_save(this,'nrl')"><i class="fa fa-floppy-o"></i> Save</button>
                <?php } else { ?>
                    <a title="Plain Editor" href="?p=<?php echo urlencode(trim(FM_PATH)) ?>&amp;edit=<?php echo urlencode($file) ?>"><i class="fa fa-text-height"></i> Plain Editor</a>
                    <button type="button" name="Save" data-url="<?php echo fm_enc($file_url) ?>" onclick="edit_save(this,'ace')"><i class="fa fa-floppy-o"></i> Save</button>
                <?php } ?>
            <?php } ?>
        </div>
        <?php
        if ($is_text && $isNormalEditor) {
            echo '<textarea id="normal-editor" rows="33" cols="120" style="width: 99.5%;">'. htmlspecialchars($content) .'</textarea>';
        } elseif ($is_text) {
            echo '<div id="editor" contenteditable="true">'. htmlspecialchars($content) .'</div>';
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

    $file_url = FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $file;
    $file_path = $path . '/' . $file;

    $mode = fileperms($path . '/' . $file);

    ?>
    <div class="path">
        <p><b><?php echo 'Change Permissions'; ?></b></p>
        <p>
            <?php echo 'Full path:'; ?> <?php echo $file_path ?><br>
        </p>
        <form action="" method="post">
            <input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
            <input type="hidden" name="chmod" value="<?php echo fm_enc($file) ?>">

            <table class="compact-table">
                <tr>
                    <td></td>
                    <td><b>Owner</b></td>
                    <td><b>Group</b></td>
                    <td><b>Other</b></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Read</b></td>
                    <td><label><input type="checkbox" name="ur" value="1"<?php echo ($mode & 00400) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="gr" value="1"<?php echo ($mode & 00040) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="or" value="1"<?php echo ($mode & 00004) ? ' checked' : '' ?>></label></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Write</b></td>
                    <td><label><input type="checkbox" name="uw" value="1"<?php echo ($mode & 00200) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="gw" value="1"<?php echo ($mode & 00020) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="ow" value="1"<?php echo ($mode & 00002) ? ' checked' : '' ?>></label></td>
                </tr>
                <tr>
                    <td style="text-align: right"><b>Execute</b></td>
                    <td><label><input type="checkbox" name="ux" value="1"<?php echo ($mode & 00100) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="gx" value="1"<?php echo ($mode & 00010) ? ' checked' : '' ?>></label></td>
                    <td><label><input type="checkbox" name="ox" value="1"<?php echo ($mode & 00001) ? ' checked' : '' ?>></label></td>
                </tr>
            </table>

            <p>
                <button type="submit" class="btn"><i class="fa fa-check-circle"></i> Change</button> &nbsp;
                <b><a href="?p=<?php echo urlencode(FM_PATH) ?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
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

$num_files = count($files);
$num_folders = count($folders);
$all_files_size = 0;
?>
<form action="" method="post">
<input type="hidden" name="p" value="<?php echo fm_enc(FM_PATH) ?>">
<input type="hidden" name="group" value="1">
<?php if(FM_TREEVIEW) { ?>
<div class="file-tree-view" id="file-tree-view">
    <div class="tree-title">Browse</div>
<?php
//file tre view
    echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "javascript:alert('You clicked on [link]');");
?>
</div>
<?php } ?>
<table class="table" id="main-table"><thead><tr>
<?php if (!FM_READONLY): ?><th style="width:3%"><label><input type="checkbox" title="Invert selection" onclick="checkbox_toggle()"></label></th><?php endif; ?>
<th>Name</th><th style="width:10%">Size</th>
<th style="width:12%">Modified</th>
<?php if (!FM_IS_WIN): ?><th style="width:6%">Perms</th><th style="width:10%">Owner</th><?php endif; ?>
<th style="width:<?php if (!FM_READONLY): ?>13<?php else: ?>6.5<?php endif; ?>%">Actions</th></tr></thead>
<?php
// link to parent folder
if ($parent !== false) {
    ?>
<tr><?php if (!FM_READONLY): ?><td></td><?php endif; ?><td colspan="<?php echo !FM_IS_WIN ? '6' : '4' ?>"><a href="?p=<?php echo urlencode($parent) ?>"><i class="fa fa-chevron-circle-left"></i> ..</a></td></tr>
<?php
}
foreach ($folders as $f) {
    $is_link = is_link($path . '/' . $f);
    $img = $is_link ? 'icon-link_folder' : 'fa fa-folder-o';
    $modif = date(FM_DATETIME_FORMAT, filemtime($path . '/' . $f));
    $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
    if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $owner = posix_getpwuid(fileowner($path . '/' . $f));
        $group = posix_getgrgid(filegroup($path . '/' . $f));
    } else {
        $owner = array('name' => '?');
        $group = array('name' => '?');
    }
    ?>
<tr>
<?php if (!FM_READONLY): ?><td><label><input type="checkbox" name="file[]" value="<?php echo fm_enc($f) ?>"></label></td><?php endif; ?>
<td><div class="filename"><a href="?p=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="<?php echo $img ?>"></i> <?php echo fm_convert_win($f) ?></a><?php echo ($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '') ?></div></td>
<td>Folder</td><td><?php echo $modif ?></td>
<?php if (!FM_IS_WIN): ?>
<td><?php if (!FM_READONLY): ?><a title="Change Permissions" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;chmod=<?php echo urlencode($f) ?>"><?php echo $perms ?></a><?php else: ?><?php echo $perms ?><?php endif; ?></td>
<td><?php echo $owner['name'] . ':' . $group['name'] ?></td>
<?php endif; ?>
<td class="inline-actions"><?php if (!FM_READONLY): ?>
<a title="Delete" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;del=<?php echo urlencode($f) ?>" onclick="return confirm('Delete folder?');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
<a title="Rename" href="#" onclick="rename('<?php echo fm_enc(FM_PATH) ?>', '<?php echo fm_enc($f) ?>');return false;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
<a title="Copy to..." href="?p=&amp;copy=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="fa fa-files-o" aria-hidden="true"></i></a>
<?php endif; ?>
<a title="Direct link" href="<?php echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f . '/') ?>" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a>
</td></tr>
    <?php
    flush();
}

foreach ($files as $f) {
    $is_link = is_link($path . '/' . $f);
    $img = $is_link ? 'fa fa-file-text-o' : fm_get_file_icon_class($path . '/' . $f);
    $modif = date(FM_DATETIME_FORMAT, filemtime($path . '/' . $f));
    $filesize_raw = filesize($path . '/' . $f);
    $filesize = fm_get_filesize($filesize_raw);
    $filelink = '?p=' . urlencode(FM_PATH) . '&amp;view=' . urlencode($f);
    $all_files_size += $filesize_raw;
    $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
    if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $owner = posix_getpwuid(fileowner($path . '/' . $f));
        $group = posix_getgrgid(filegroup($path . '/' . $f));
    } else {
        $owner = array('name' => '?');
        $group = array('name' => '?');
    }
    ?>
<tr>
<?php if (!FM_READONLY): ?><td><label><input type="checkbox" name="file[]" value="<?php echo fm_enc($f) ?>"></label></td><?php endif; ?>
<td><div class="filename"><a href="<?php echo $filelink ?>" title="File info"><i class="<?php echo $img ?>"></i> <?php echo fm_convert_win($f) ?></a><?php echo ($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : '') ?></div></td>
<td><span title="<?php printf('%s bytes', $filesize_raw) ?>"><?php echo $filesize ?></span></td>
<td><?php echo $modif ?></td>
<?php if (!FM_IS_WIN): ?>
<td><?php if (!FM_READONLY): ?><a title="<?php echo 'Change Permissions' ?>" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;chmod=<?php echo urlencode($f) ?>"><?php echo $perms ?></a><?php else: ?><?php echo $perms ?><?php endif; ?></td>
<td><?php echo fm_enc($owner['name'] . ':' . $group['name']) ?></td>
<?php endif; ?>
<td class="inline-actions">
<?php if (!FM_READONLY): ?>
<a title="Delete" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;del=<?php echo urlencode($f) ?>" onclick="return confirm('Delete file?');"><i class="fa fa-trash-o"></i></a>
<a title="Rename" href="#" onclick="rename('<?php echo fm_enc(FM_PATH) ?>', '<?php echo fm_enc($f) ?>');return false;"><i class="fa fa-pencil-square-o"></i></a>
<a title="Copy to..." href="?p=<?php echo urlencode(FM_PATH) ?>&amp;copy=<?php echo urlencode(trim(FM_PATH . '/' . $f, '/')) ?>"><i class="fa fa-files-o"></i></a>
<?php endif; ?>
<a title="Direct link" href="<?php echo fm_enc(FM_ROOT_URL . (FM_PATH != '' ? '/' . FM_PATH : '') . '/' . $f) ?>" target="_blank"><i class="fa fa-link"></i></a>
<a title="Download" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;dl=<?php echo urlencode($f) ?>"><i class="fa fa-download"></i></a>
</td></tr>
    <?php
    flush();
}

if (empty($folders) && empty($files)) {
    ?>
<tr><?php if (!FM_READONLY): ?><td></td><?php endif; ?><td colspan="<?php echo !FM_IS_WIN ? '6' : '4' ?>"><em><?php echo 'Folder is empty' ?></em></td></tr>
<?php
} else {
    ?>
<tr><?php if (!FM_READONLY): ?><td class="gray"></td><?php endif; ?><td class="gray" colspan="<?php echo !FM_IS_WIN ? '6' : '4' ?>">
Full size: <span title="<?php printf('%s bytes', $all_files_size) ?>"><?php echo fm_get_filesize($all_files_size) ?></span>,
files: <?php echo $num_files ?>,
folders: <?php echo $num_folders ?>
</td></tr>
<?php
}
?>
</table>
<?php if (!FM_READONLY): ?>
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
<?php endif; ?>
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
        $ok = true;
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
        $ok = true;
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
        $mime = finfo_file($finfo, $file_path);
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
    $path = str_replace(array('../', '..\\'), '', $path);
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
                $zip_name = zip_entry_name($zip_entry);
                $zip_folder = substr($zip_name, -1) == '/';
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
function scan($dir){
    $files = array();
    $_dir = $dir;
    $dir = FM_ROOT_PATH.'/'.$dir;
    // Is there actually such a folder/file?
    if(file_exists($dir)){
        foreach(scandir($dir) as $f) {
            if(!$f || $f[0] == '.') {
                continue; // Ignore hidden files
            }

            if(is_dir($dir . '/' . $f)) {
                // The path is a folder
                $files[] = array(
                    "name" => $f,
                    "type" => "folder",
                    "path" => $_dir.'/'.$f,
                    "items" => scan($dir . '/' . $f), // Recursively get the contents of the folder
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
function php_file_tree_dir($directory, $first_call = true) {
	// Recursive function called by php_file_tree() to list directories/files

	$php_file_tree = "";
	// Get and sort directories/files
	if( function_exists("scandir") ) $file = scandir($directory);
	natcasesort($file);
	// Make directories first
	$files = $dirs = array();
	foreach($file as $this_file) {
		if( is_dir("$directory/$this_file" ) ) {
      if(!in_array($this_file, $GLOBALS['exclude_folders'])){
          $dirs[] = $this_file;
      }
    } else {
      $files[] = $this_file;
    }
	}
	$file = array_merge($dirs, $files);

	if( count($file) > 2 ) { // Use 2 instead of 0 to account for . and .. "directories"
		$php_file_tree = "<ul";
		if( $first_call ) { $php_file_tree .= " class=\"php-file-tree\""; $first_call = false; }
		$php_file_tree .= ">";
		foreach( $file as $this_file ) {
			if( $this_file != "." && $this_file != ".." ) {
				if( is_dir("$directory/$this_file") ) {
					// Directory
					$php_file_tree .= "<li class=\"pft-directory\"><i class=\"fa fa-folder-o\"></i><a href=\"#\">" . htmlspecialchars($this_file) . "</a>";
					$php_file_tree .= php_file_tree_dir("$directory/$this_file", false);
					$php_file_tree .= "</li>";
				} else {
					// File
                    $ext = fm_get_file_icon_class($this_file);
                    $path = str_replace($_SERVER['DOCUMENT_ROOT'],"",$directory);
					$link = "?p="."$path" ."&view=".urlencode($this_file);
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
function php_file_tree($directory) {
    // Remove trailing slash
    $code = "";
    if( substr($directory, -1) == "/" ) $directory = substr($directory, 0, strlen($directory) - 1);
    if(function_exists('php_file_tree_dir')) {
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
    $_SESSION['status'] = $status;
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
        case 'ico': case 'gif': case 'jpg': case 'jpeg': case 'jpc': case 'jp2':
        case 'jpx': case 'xbm': case 'wbmp': case 'png': case 'bmp': case 'tif':
        case 'tiff': case 'svg':
            $img = 'fa fa-picture-o';
            break;
        case 'passwd': case 'ftpquota': case 'sql': case 'js': case 'json': case 'sh':
        case 'config': case 'twig': case 'tpl': case 'md': case 'gitignore':
        case 'c': case 'cpp': case 'cs': case 'py': case 'map': case 'lock': case 'dtd':
            $img = 'fa fa-file-code-o';
            break;
        case 'txt': case 'ini': case 'conf': case 'log': case 'htaccess':
            $img = 'fa fa-file-text-o';
            break;
        case 'css': case 'less': case 'sass': case 'scss':
            $img = 'fa fa-css3';
            break;
        case 'zip': case 'rar': case 'gz': case 'tar': case '7z':
            $img = 'fa fa-file-archive-o';
            break;
        case 'php': case 'php4': case 'php5': case 'phps': case 'phtml':
            $img = 'fa fa-code';
            break;
        case 'htm': case 'html': case 'shtml': case 'xhtml':
            $img = 'fa fa-html5';
            break;
        case 'xml': case 'xsl':
            $img = 'fa fa-file-excel-o';
            break;
        case 'wav': case 'mp3': case 'mp2': case 'm4a': case 'aac': case 'ogg':
        case 'oga': case 'wma': case 'mka': case 'flac': case 'ac3': case 'tds':
            $img = 'fa fa-music';
            break;
        case 'm3u': case 'm3u8': case 'pls': case 'cue':
            $img = 'fa fa-headphones';
            break;
        case 'avi': case 'mpg': case 'mpeg': case 'mp4': case 'm4v': case 'flv':
        case 'f4v': case 'ogm': case 'ogv': case 'mov': case 'mkv': case '3gp':
        case 'asf': case 'wmv':
            $img = 'fa fa-file-video-o';
            break;
        case 'eml': case 'msg':
            $img = 'fa fa-envelope-o';
            break;
        case 'xls': case 'xlsx':
            $img = 'fa fa-file-excel-o';
            break;
        case 'csv':
            $img = 'fa fa-file-text-o';
            break;
        case 'bak':
            $img = 'fa fa-clipboard';
            break;
        case 'doc': case 'docx':
            $img = 'fa fa-file-word-o';
            break;
        case 'ppt': case 'pptx':
            $img = 'fa fa-file-powerpoint-o';
            break;
        case 'ttf': case 'ttc': case 'otf': case 'woff':case 'woff2': case 'eot': case 'fon':
            $img = 'fa fa-font';
            break;
        case 'pdf':
            $img = 'fa fa-file-pdf-o';
            break;
        case 'psd': case 'ai': case 'eps': case 'fla': case 'swf':
            $img = 'fa fa-file-image-o';
            break;
        case 'exe': case 'msi':
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
        'message/rfc822',
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
        'changelog',
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
        $path = fm_clean_path($path);
        $root_url = "<a href='?p='><i class='fa fa-home' aria-hidden='true' title='" . FM_ROOT_PATH . "'></i></a>";
        $sep = '<i class="fa fa-caret-right"></i>';
        if ($path != '') {
            $exploded = explode('/', $path);
            $count = count($exploded);
            $array = array();
            $parent = '';
            for ($i = 0; $i < $count; $i++) {
                $parent = trim($parent . '/' . $exploded[$i], '/');
                $parent_enc = urlencode($parent);
                $array[] = "<a href='?p={$parent_enc}'>" . fm_enc(fm_convert_win($exploded[$i])) . "</a>";
            }
            $root_url .= $sep . implode($sep, $array);
        }
        echo '<div class="break-word float-left">' . $root_url . '</div>';
        ?>

        <div class="float-right">
        <?php if (!FM_READONLY): ?>
        <a title="Search" href="javascript:showSearch('<?php echo urlencode(FM_PATH) ?>')"><i class="fa fa-search"></i></a>
        <a title="Upload files" href="?p=<?php echo urlencode(FM_PATH) ?>&amp;upload"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
        <a title="New folder" href="#createNewItem" ><i class="fa fa-plus-square"></i></a>
        <?php endif; ?>
        <?php if (FM_USE_AUTH): ?><a title="Logout" href="?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i></a><?php endif; ?>
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
    <link rel="icon" href="<?php echo FM_SELF_URL ?>?img=favicon" type="image/png">
    <link rel="shortcut icon" href="<?php echo FM_SELF_URL ?>?img=favicon" type="image/png">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style type="text/css">a img,img{border:none}.filename,td,th{white-space:nowrap}.close,.close:focus,.close:hover,.php-file-tree a,a{text-decoration:none}a,body,code,div,em,form,html,img,label,li,ol,p,pre,small,span,strong,table,td,th,tr,ul{margin:0;padding:0;vertical-align:baseline;outline:0;font-size:100%;background:0 0;border:none;text-decoration:none}p,table,ul{margin-bottom:10px}html{overflow-y:scroll}body{padding:0;font:13px/16px Tahoma,Arial,sans-serif;color:#222;background:#F7F7F7;margin:50px 30px 0}button,input,select,textarea{font-size:inherit;font-family:inherit}a{color:#296ea3}a:hover{color:#b00}img{vertical-align:middle}span{color:#777}small{font-size:11px;color:#999}ul{list-style-type:none;margin-left:0}ul li{padding:3px 0}table{border-collapse:collapse;border-spacing:0;width:100%}.file-tree-view+#main-table{width:75%!important;float:left}td,th{padding:4px 7px;text-align:left;vertical-align:top;border:1px solid #ddd;background:#fff}td.gray,th{background-color:#eee}td.gray span{color:#222}tr:hover td{background-color:#f5f5f5}tr:hover td.gray{background-color:#eee}.table{width:100%;max-width:100%;margin-bottom:1rem}.table td,.table th{padding:.55rem;vertical-align:top;border-top:1px solid #ddd}.table thead th{vertical-align:bottom;border-bottom:2px solid #eceeef}.table tbody+tbody{border-top:2px solid #eceeef}.table .table{background-color:#fff}code,pre{display:block;margin-bottom:10px;font:13px/16px Consolas,'Courier New',Courier,monospace;border:1px dashed #ccc;padding:5px;overflow:auto}.hidden,.modal{display:none}.btn,.close{font-weight:700}pre.with-hljs{padding:0}pre.with-hljs code{margin:0;border:0;overflow:visible}code.maxheight,pre.maxheight{max-height:512px}input[type=checkbox]{margin:0;padding:0}.message,.path{padding:4px 7px;border:1px solid #ddd;background-color:#fff}.fa.fa-caret-right{font-size:1.2em;margin:0 4px;vertical-align:middle;color:#ececec}.fa.fa-home{font-size:1.2em;vertical-align:bottom}#wrapper{min-width:400px;margin:0 auto}.path{margin-bottom:10px}.right{text-align:right}.center,.close,.login-form{text-align:center}.float-right{float:right}.float-left{float:left}.message.ok{border-color:green;color:green}.message.error{border-color:red;color:red}.message.alert{border-color:orange;color:orange}.btn{border:0;background:0 0;padding:0;margin:0;color:#296ea3;cursor:pointer}.btn:hover{color:#b00}.preview-img{max-width:100%;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAKklEQVR42mL5//8/Azbw+PFjrOJMDCSCUQ3EABZc4S0rKzsaSvTTABBgAMyfCMsY4B9iAAAAAElFTkSuQmCC)}.inline-actions>a>i{font-size:1em;margin-left:5px;background:#3785c1;color:#fff;padding:3px;border-radius:3px}.preview-video{position:relative;max-width:100%;height:0;padding-bottom:62.5%;margin-bottom:10px}.preview-video video{position:absolute;width:100%;height:100%;left:0;top:0;background:#000}.compact-table{border:0;width:auto}.compact-table td,.compact-table th{width:100px;border:0;text-align:center}.compact-table tr:hover td{background-color:#fff}.filename{max-width:420px;overflow:hidden;text-overflow:ellipsis}.break-word{word-wrap:break-word;margin-left:30px}.break-word.float-left a{color:#7d7d7d}.break-word+.float-right{padding-right:30px;position:relative}.break-word+.float-right>a{color:#7d7d7d;font-size:1.2em;margin-right:4px}.modal{position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}#editor,.edit-file-actions{position:absolute;right:30px}.modal-content{background-color:#fefefe;margin:auto;padding:20px;border:1px solid #888;width:80%}.close:focus,.close:hover{color:#000;cursor:pointer}#editor{top:50px;bottom:5px;left:30px}.edit-file-actions{top:0;background:#fff;margin-top:5px}.edit-file-actions>a,.edit-file-actions>button{background:#fff;padding:5px 15px;cursor:pointer;color:#296ea3;border:1px solid #296ea3}.group-btn{background:#fff;padding:2px 6px;border:1px solid;cursor:pointer;color:#296ea3}.main-nav{position:fixed;top:0;left:0;padding:10px 30px 10px 1px;width:100%;background:#fff;color:#000;border:0;box-shadow:0 4px 5px 0 rgba(0,0,0,.14),0 1px 10px 0 rgba(0,0,0,.12),0 2px 4px -1px rgba(0,0,0,.2)}.login-form{width:320px;margin:0 auto;box-shadow:0 8px 10px 1px rgba(0,0,0,.14),0 3px 14px 2px rgba(0,0,0,.12),0 5px 5px -3px rgba(0,0,0,.2)}.login-form label,.path.login-form input{padding:8px;margin:10px}.footer-links{background:0 0;border:0;clear:both}select[name=lang]{border:none;position:relative;text-transform:uppercase;left:-30%;top:12px;color:silver}input[type=search]{height:30px;margin:5px;width:80%;border:1px solid #ccc}.path.login-form input[type=submit]{background-color:#4285f4;color:#fff;border:1px solid;border-radius:2px;font-weight:700;cursor:pointer}.modalDialog{position:fixed;font-family:Arial,Helvetica,sans-serif;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.8);z-index:99999;opacity:0;-webkit-transition:opacity .4s ease-in;-moz-transition:opacity .4s ease-in;transition:opacity .4s ease-in;pointer-events:none}.modalDialog:target{opacity:1;pointer-events:auto}.modalDialog>.model-wrapper{max-width:400px;position:relative;margin:10% auto;padding:15px;border-radius:2px;background:#fff}.close{float:right;background:#fff;color:#000;line-height:25px;position:absolute;right:0;top:0;width:24px;border-radius:0 5px 0 0;font-size:18px}.close:hover{background:#e4e4e4}.modalDialog p{line-height:30px}div#searchresultWrapper{max-height:320px;overflow:auto}div#searchresultWrapper li{margin:8px 0;list-style:none}li.file:before,li.folder:before{font:normal normal normal 14px/1 FontAwesome;content:"\f016";margin-right:5px}li.folder:before{content:"\f114"}i.fa.fa-folder-o{color:#eeaf4b}i.fa.fa-picture-o{color:#26b99a}i.fa.fa-file-archive-o{color:#da7d7d}.footer-links i.fa.fa-file-archive-o{color:#296ea3}i.fa.fa-css3{color:#f36fa0}i.fa.fa-file-code-o{color:#ec6630}i.fa.fa-code{color:#cc4b4c}i.fa.fa-file-text-o{color:#0096e6}i.fa.fa-html5{color:#d75e72}i.fa.fa-file-excel-o{color:#09c55d}i.fa.fa-file-powerpoint-o{color:#f6712e}.file-tree-view{width:24%;float:left;overflow:auto;border:1px solid #ddd;border-right:0;background:#fff}.file-tree-view .tree-title{background:#eee;padding:9px 2px 9px 10px;font-weight:700}.file-tree-view ul{margin-left:15px;margin-bottom:0}.file-tree-view i{padding-right:3px}.php-file-tree{font-size:100%;letter-spacing:1px;line-height:1.5;margin-left:5px!important}.php-file-tree a{color:#296ea3}.php-file-tree A:hover{color:#b00}.php-file-tree .open{font-style:italic;color:#2183ce}.php-file-tree .closed{font-style:normal}#file-tree-view::-webkit-scrollbar{width:10px;background-color:#F5F5F5}#file-tree-view::-webkit-scrollbar-track{border-radius:10px;background:rgba(0,0,0,.1);border:1px solid #ccc}#file-tree-view::-webkit-scrollbar-thumb{border-radius:10px;background:linear-gradient(left,#fff,#e4e4e4);border:1px solid #aaa}#file-tree-view::-webkit-scrollbar-thumb:hover{background:#fff}#file-tree-view::-webkit-scrollbar-thumb:active{background:linear-gradient(left,#22ADD4,#1E98BA)}</style>
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
    <link rel="icon" href="<?php echo FM_SELF_URL ?>?img=favicon" type="image/png">
    <link rel="shortcut icon" href="<?php echo FM_SELF_URL ?>?img=favicon" type="image/png">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <?php if (isset($_GET['view']) && FM_USE_HIGHLIGHTJS): ?>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/<?php echo FM_HIGHLIGHTJS_STYLE ?>.min.css">
    <?php endif; ?>
    <style type="text/css">a img,img{border:none}.filename,td,th{white-space:nowrap}.close,.close:focus,.close:hover,.php-file-tree a,a{text-decoration:none}a,body,code,div,em,form,html,img,label,li,ol,p,pre,small,span,strong,table,td,th,tr,ul{margin:0;padding:0;vertical-align:baseline;outline:0;font-size:100%;background:0 0;border:none;text-decoration:none}p,table,ul{margin-bottom:10px}html{overflow-y:scroll}body{padding:0;font:13px/16px Tahoma,Arial,sans-serif;color:#222;background:#F7F7F7;margin:50px 30px 0}button,input,select,textarea{font-size:inherit;font-family:inherit}a{color:#296ea3}a:hover{color:#b00}img{vertical-align:middle}span{color:#777}small{font-size:11px;color:#999}ul{list-style-type:none;margin-left:0}ul li{padding:3px 0}table{border-collapse:collapse;border-spacing:0;width:100%}.file-tree-view+#main-table{width:75%!important;float:left}td,th{padding:4px 7px;text-align:left;vertical-align:top;border:1px solid #ddd;background:#fff}td.gray,th{background-color:#eee}td.gray span{color:#222}tr:hover td{background-color:#f5f5f5}tr:hover td.gray{background-color:#eee}.table{width:100%;max-width:100%;margin-bottom:1rem}.table td,.table th{padding:.55rem;vertical-align:top;border-top:1px solid #ddd}.table thead th{vertical-align:bottom;border-bottom:2px solid #eceeef}.table tbody+tbody{border-top:2px solid #eceeef}.table .table{background-color:#fff}code,pre{display:block;margin-bottom:10px;font:13px/16px Consolas,'Courier New',Courier,monospace;border:1px dashed #ccc;padding:5px;overflow:auto}.hidden,.modal{display:none}.btn,.close{font-weight:700}pre.with-hljs{padding:0}pre.with-hljs code{margin:0;border:0;overflow:visible}code.maxheight,pre.maxheight{max-height:512px}input[type=checkbox]{margin:0;padding:0}.message,.path{padding:4px 7px;border:1px solid #ddd;background-color:#fff}.fa.fa-caret-right{font-size:1.2em;margin:0 4px;vertical-align:middle;color:#ececec}.fa.fa-home{font-size:1.2em;vertical-align:bottom}#wrapper{min-width:400px;margin:0 auto}.path{margin-bottom:10px}.right{text-align:right}.center,.close,.login-form{text-align:center}.float-right{float:right}.float-left{float:left}.message.ok{border-color:green;color:green}.message.error{border-color:red;color:red}.message.alert{border-color:orange;color:orange}.btn{border:0;background:0 0;padding:0;margin:0;color:#296ea3;cursor:pointer}.btn:hover{color:#b00}.preview-img{max-width:100%;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAKklEQVR42mL5//8/Azbw+PFjrOJMDCSCUQ3EABZc4S0rKzsaSvTTABBgAMyfCMsY4B9iAAAAAElFTkSuQmCC)}.inline-actions>a>i{font-size:1em;margin-left:5px;background:#3785c1;color:#fff;padding:3px;border-radius:3px}.preview-video{position:relative;max-width:100%;height:0;padding-bottom:62.5%;margin-bottom:10px}.preview-video video{position:absolute;width:100%;height:100%;left:0;top:0;background:#000}.compact-table{border:0;width:auto}.compact-table td,.compact-table th{width:100px;border:0;text-align:center}.compact-table tr:hover td{background-color:#fff}.filename{max-width:420px;overflow:hidden;text-overflow:ellipsis}.break-word{word-wrap:break-word;margin-left:30px}.break-word.float-left a{color:#7d7d7d}.break-word+.float-right{padding-right:30px;position:relative}.break-word+.float-right>a{color:#7d7d7d;font-size:1.2em;margin-right:4px}.modal{position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}#editor,.edit-file-actions{position:absolute;right:30px}.modal-content{background-color:#fefefe;margin:auto;padding:20px;border:1px solid #888;width:80%}.close:focus,.close:hover{color:#000;cursor:pointer}#editor{top:50px;bottom:5px;left:30px}.edit-file-actions{top:0;background:#fff;margin-top:5px}.edit-file-actions>a,.edit-file-actions>button{background:#fff;padding:5px 15px;cursor:pointer;color:#296ea3;border:1px solid #296ea3}.group-btn{background:#fff;padding:2px 6px;border:1px solid;cursor:pointer;color:#296ea3}.main-nav{position:fixed;top:0;left:0;padding:10px 30px 10px 1px;width:100%;background:#fff;color:#000;border:0;box-shadow:0 4px 5px 0 rgba(0,0,0,.14),0 1px 10px 0 rgba(0,0,0,.12),0 2px 4px -1px rgba(0,0,0,.2)}.login-form{width:320px;margin:0 auto;box-shadow:0 8px 10px 1px rgba(0,0,0,.14),0 3px 14px 2px rgba(0,0,0,.12),0 5px 5px -3px rgba(0,0,0,.2)}.login-form label,.path.login-form input{padding:8px;margin:10px}.footer-links{background:0 0;border:0;clear:both}select[name=lang]{border:none;position:relative;text-transform:uppercase;left:-30%;top:12px;color:silver}input[type=search]{height:30px;margin:5px;width:80%;border:1px solid #ccc}.path.login-form input[type=submit]{background-color:#4285f4;color:#fff;border:1px solid;border-radius:2px;font-weight:700;cursor:pointer}.modalDialog{position:fixed;font-family:Arial,Helvetica,sans-serif;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.8);z-index:99999;opacity:0;-webkit-transition:opacity .4s ease-in;-moz-transition:opacity .4s ease-in;transition:opacity .4s ease-in;pointer-events:none}.modalDialog:target{opacity:1;pointer-events:auto}.modalDialog>.model-wrapper{max-width:400px;position:relative;margin:10% auto;padding:15px;border-radius:2px;background:#fff}.close{float:right;background:#fff;color:#000;line-height:25px;position:absolute;right:0;top:0;width:24px;border-radius:0 5px 0 0;font-size:18px}.close:hover{background:#e4e4e4}.modalDialog p{line-height:30px}div#searchresultWrapper{max-height:320px;overflow:auto}div#searchresultWrapper li{margin:8px 0;list-style:none}li.file:before,li.folder:before{font:normal normal normal 14px/1 FontAwesome;content:"\f016";margin-right:5px}li.folder:before{content:"\f114"}i.fa.fa-folder-o{color:#eeaf4b}i.fa.fa-picture-o{color:#26b99a}i.fa.fa-file-archive-o{color:#da7d7d}.footer-links i.fa.fa-file-archive-o{color:#296ea3}i.fa.fa-css3{color:#f36fa0}i.fa.fa-file-code-o{color:#ec6630}i.fa.fa-code{color:#cc4b4c}i.fa.fa-file-text-o{color:#0096e6}i.fa.fa-html5{color:#d75e72}i.fa.fa-file-excel-o{color:#09c55d}i.fa.fa-file-powerpoint-o{color:#f6712e}.file-tree-view{width:24%;float:left;overflow:auto;border:1px solid #ddd;border-right:0;background:#fff}.file-tree-view .tree-title{background:#eee;padding:9px 2px 9px 10px;font-weight:700}.file-tree-view ul{margin-left:15px;margin-bottom:0}.file-tree-view i{padding-right:3px}.php-file-tree{font-size:100%;letter-spacing:1px;line-height:1.5;margin-left:5px!important}.php-file-tree a{color:#296ea3}.php-file-tree A:hover{color:#b00}.php-file-tree .open{font-style:italic;color:#2183ce}.php-file-tree .closed{font-style:normal}#file-tree-view::-webkit-scrollbar{width:10px;background-color:#F5F5F5}#file-tree-view::-webkit-scrollbar-track{border-radius:10px;background:rgba(0,0,0,.1);border:1px solid #ccc}#file-tree-view::-webkit-scrollbar-thumb{border-radius:10px;background:linear-gradient(left,#fff,#e4e4e4);border:1px solid #aaa}#file-tree-view::-webkit-scrollbar-thumb:hover{background:#fff}#file-tree-view::-webkit-scrollbar-thumb:active{background:linear-gradient(left,#22ADD4,#1E98BA)}</style>
</head>
<body>
<div id="wrapper">
  <div id="createNewItem" class="modalDialog"><div class="model-wrapper"><a href="#close" title="Close" class="close">X</a><h2>Create New Item</h2><p>
        <label for="newfile">Item Type &nbsp; : </label><input type="radio" name="newfile" id="newfile" value="file">File <input type="radio" name="newfile" value="folder" checked> Folder<br><label for="newfilename">Item Name : </label><input type="text" name="newfilename" id="newfilename" value=""><br>
        <input type="submit" name="submit" class="group-btn" value="Create Now" onclick="newfolder('<?php echo fm_enc(FM_PATH) ?>');return false;"></p></div></div>
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
<?php if (isset($_GET['view']) && FM_USE_HIGHLIGHTJS): ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<?php endif; ?>
<?php if (isset($_GET['edit']) && isset($_GET['env']) && FM_EDIT_FILE): ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js"></script>
<script>var editor = ace.edit("editor");editor.getSession().setMode("ace/mode/javascript");</script>
<?php endif; ?>
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
    $expires_time = gmdate('D, d M Y 00:00:00', strtotime('+1 day')) . ' GMT';

    $img = trim($img);
    $images = fm_get_images();
    $image = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg==';
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
        'favicon' => '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',
        'logo1' => 'https://image.ibb.co/k92AFQ/h3k_logo_dark.png',
        'logo' => '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',
        'sprites' => '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
RK5CYII=',
    );
}
?>
