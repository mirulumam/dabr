<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="templates/mobile/mobile.css" />
  <base href="<?php echo BASE_URL; ?>" />
</head>
<body>
<div id="menu" class="menu"><ul id="menu-main">
  <li><a href="home">Home</a></li>
<?php if (user_is_authenticated()): ?>
  <li><a href="trends">Trends</a></li>
  <li><a href="replies">Replies</a></li>
<?php else: ?>
  <li><a href="oauth">OAuth login</a></li>
<?php endif; ?>
</ul></div>
<form method="post" action="update">
  <textarea id="status" name="status" rows="3" style="width:100%; max-width: 400px;"></textarea><br />
  <input type="submit" value="Update" />
</form>
<?php echo $content; ?>
</body>
</html>