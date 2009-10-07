<?php
/**
 * Quick and dirty Markdown renderer
 */
require('lib/config.php');
require('lib/markdown.php');

// Convert ?q into a file path
$q = $_GET['q'] ? $_GET['q'] : 'index.md';
$q = str_replace($base_path, '', $q);
$file = $base_path . '/' . $q;

// Try to prevent naughty files paths
if (FALSE && realpath($file) !== $file) {
    header('HTTP/1.1 403 Forbidden');
    echo "Forbidden"; exit;
}

if (!is_file($file)) {
    
    // No file found, so panic.
    $title = "Not found";
    $text = 'file not found';

} else {

    // Grab the file contents and render with Markdown
    $data = file_get_contents($file);
    $text = Markdown($data);

    // Try to lift the first line as page title, if it's an H1
    $lines = explode("\n", $data);
    if (count($lines) > 0 && 0 == strpos($lines[0], '# ')) {
        $parts = explode(" ", $lines[0]);
        array_shift($parts);
        $title = join(' ', $parts);
    }

}
?>
<html>
    <head>
        <title><?=htmlspecialchars($title)?></title>
        <style>
            article, footer, header, hgroup, nav, section { display: block; }
        </style>
        <link type="text/css" rel="stylesheet" href="<?=$base_url?>/css/render.css" />
        <script src="<?=$base_url?>/js/jquery-1.3.2.min.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/md5-min.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/html5.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/outliner.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/render.js" type="text/javascript"/></script>
    </head>
    <body>
        <article>
            <section><?=$text?></section>
        </article>
    </body>
</html>
