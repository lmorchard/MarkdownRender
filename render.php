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
$mtime = filemtime($file);

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
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <title><?=htmlspecialchars($title)?></title>
        <style>
            article, footer, header, hgroup, nav, section { display: block; }
        </style>
        <link type="text/css" rel="stylesheet" href="<?=$base_url?>/css/render.css" />
        <script type="text/javascript">
            var disqus_shortname = '<?= htmlspecialchars($disqus_shortname) ?>';
            <?php if ($disqus_developer): ?>
                var disqus_developer = 1;
            <?php endif ?>
        </script>
        <!--[if IE]>
            <script src="<?=$base_url?>/js/html5.js" type="text/javascript"/></script>
        <![endif]-->
        <script src="<?=$base_url?>/js/jquery-1.3.2.min.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/md5-min.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/outliner.js" type="text/javascript"/></script>
        <script src="<?=$base_url?>/js/render.js" type="text/javascript"/></script>
    </head>
    <body>
        <header>
            <h1><a rel="home" title="0xDECAFBAD" href="http://decafbad.com/">0xDECAFBAD</a></h1>
            <span>It’s all spinning wheels and self-doubt until the first pot of coffee.</span>
        </header>

        <article>
            <time datetime="<?=date("c", $mtime)?>" pubdate><span><?=date("D, Y M d @ H:i O", $mtime)?></span></time>

            <section><?=$text?></section>
                
            <section class="comments">

                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>

            </section>
        </article>
    </body>
</html>
