<html>
<head>
<meta charset="utf-8" />

<link rel="stylesheet" type="text/css" href="/views/templates/index.css">

</head>
<body bgcolor="#DDECE9">






<header>
<h1>&lt;header&gt; Шапка сайта</h1><br><br>

</header>
<div style="height: 100%;">
<nav class="side_left">
<?php
	include $menu;
    
?>
</nav>
<aside class="side_right"><h2>&lt;sidebar&gt;<br>сайдбар</h2></aside>
<article>
<?php
//include 'views/get_kurs/index.php';
//echo $view;
include $view;
?>
</article>
</div>
<footer>
<p><b>&lt;footer&gt;</b>Здесь обычно пишут, что права защищены.
 Год и.т.п. Копирование запрещено))</p>
</footer>
</body>
</html>
