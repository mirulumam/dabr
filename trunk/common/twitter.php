<?php

menu_register(array(
  '' => array(
    'callback' => 'twitter_friends_page',
  ),
  'status' => array(
    'hidden' => true,
    'callback' => 'twitter_status_page',
  ),
  'update' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_update',
  ),
  'public' => array(
    'callback' => 'twitter_public_page',
  ),
  'replies' => array(
    'security' => true,
    'callback' => 'twitter_replies_page',
  ),
  'favourites' => array(
    'security' => true,
    'callback' =>  'twitter_favourites_page',
  ),
  'directs' => array(
    'security' => true,
    'callback' => 'twitter_directs_page',
  ),
  'search' => array(
    'callback' => 'twitter_search_page',
  ),
  'user' => array(
    'hidden' => true,
    'callback' => 'twitter_user_page',
  ),
  'follow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'unfollow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'followers' => array(
    'callback' => 'twitter_followers_page',
  ),
  'delete' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_delete_page',
  ),
));

function twitter_process($url, $post_data = false) {
  $ch = curl_init($url);

  if($post_data !== false) {
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
  }

  if(user_is_authenticated())
    curl_setopt($ch, CURLOPT_USERPWD, $GLOBALS['user']['username'].':'.$GLOBALS['user']['password']);

  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_NOBODY, 0);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'dabr');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $response = curl_exec($ch);
  $response_info=curl_getinfo($ch);
  curl_close($ch);

  switch( intval( $response_info['http_code'] ) ) {
    case 200:
      return json_decode($response);
    case 401:
      user_logout();
      theme('error', '<p>Error: Login credentials incorrect.</p>');
    default:
      $result = json_decode($response);
      $result = $result->error ? $result->error : $response;
      theme('error', "<h2>Error {$http_code}</h2><p>{$result}</p><hr><p>$url</p>");
  }
}

function twitter_isgd($text) {
  return preg_replace_callback('#(http://|www)[^ ]{33,1950}\b#', 'twitter_isgd_callback', $text);
}

function twitter_isgd_callback($match) {
  $request = 'http://is.gd/api.php?longurl='.urlencode($match[0]);
  return twitter_fetch($request);
}

function twitter_fetch($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}

function twitter_parse_links_callback($matches) {
  $url = $matches[1];
  return theme('external_link', $url);
}

function twitter_parse_tags($input) {
  $out = preg_replace_callback('#([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)(?=\b)#is', 'twitter_parse_links_callback', $input);
  $out = preg_replace('#(@([a-z_A-Z0-9]+))#', '@<a href="user/$2">$2</a>', $out);
  $out = preg_replace('#(\\#([a-z_A-Z0-9:_-]+))#', '<a href="search/?query=%23$2">$0</a>', $out);
  return $out;
}

function format_interval($timestamp, $granularity = 2) {
  $units = array(
    'years' => 31536000,
    'days' => 86400,
    'hours' => 3600,
    'min' => 60,
    'sec' => 1
  );
  $output = '';
  foreach ($units as $key => $value) {
    if ($timestamp >= $value) {
      $output .= ($output ? ' ' : '').floor($timestamp / $value).' '.$key;
      $timestamp %= $value;
      $granularity--;
    }
    if ($granularity == 0) {
      break;
    }
  }
  return $output ? $output : '0 sec';
}

function twitter_status_page($query) {
  $id = (int) $query[1];
  if ($id) {
    $request = "http://twitter.com/statuses/show/{$id}.json";
    $tl = twitter_process($request, $id);
    $content = theme('status', $tl);
    theme('page', "Status $id", $content);
  }
}

function twitter_delete_page($query) {
  $id = (int) $query[1];
  if ($id) {
    $request = "http://twitter.com/statuses/destroy/{$id}.json";
    $tl = twitter_process($request, 1);
    header('Location: '. BASE_URL);
    exit();
  }
}

function twitter_follow_page($query) {
  $user = $query[1];
  if ($user) {
    if($query[0] == 'follow'){
      $request = "http://twitter.com/friendships/create/{$user}.json";
    } else {
      $request = "http://twitter.com/friendships/destroy/{$user}.json";
    }
    twitter_process($request, 1);
    header('Location: '. BASE_URL);
    exit();
  }
}

function twitter_followers_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = $GLOBALS['user']['username'];
  }
  $request = "http://twitter.com/statuses/followers/{$user}.json";
  $tl = twitter_process($request);
  $content = theme('followers', $tl);
  theme('page', 'Followers', $content);
}

function twitter_update() {
  $status = twitter_isgd(stripslashes(trim($_POST['status'])));
  if ($status) {
    $request = 'http://twitter.com/statuses/update.json';
    $post_data = 'source=dabr&status='.urlencode($status);
    $b = twitter_process($request, $post_data);
  }
  header('Location: '. BASE_URL);
  exit();
}

function twitter_public_page() {
  $request = 'http://twitter.com/statuses/public_timeline.json';
  $content = theme('status_form');
  $content .= theme('timeline', twitter_process($request));
  theme('page', 'Public Timeline', $content);
}

function twitter_replies_page() {
  $request = 'http://twitter.com/statuses/replies.json';
  $tl = twitter_process($request);
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Replies', $content);
}

function twitter_directs_page() {
  $request = 'http://twitter.com/direct_messages.json';
  $tl = twitter_process($request);
  $content = theme('status_form');
  $content .= theme('directs', $tl);
  theme('page', 'Direct Messages', $content);
}

function twitter_search_page() {
  $search_query = $_GET['query'];
  $content = theme('search_form');
  if ($search_query) {
    $request = 'http://search.twitter.com/search.json?q=' . urlencode($search_query);
    $tl = twitter_process($request);
    $content .= theme('search_results', $tl);
  }
  theme('page', 'Search', $content);
}

function twitter_user_page($query) {
  $screen_name = $query[1];
  if ($screen_name) {
    $request = "http://twitter.com/statuses/user_timeline/{$screen_name}.json";
    $tl = twitter_process($request);
    $content = theme('user', $tl);
    theme('page', "User {$screen_name}", $content);
  } else {
    // TODO: user search screen
  }
}

function twitter_favourites_page($query) {
  $screen_name = $query[1];
  if (!$screen_name) {
    $screen_name = $GLOBALS['user']['username'];
  }
  $request = "http://twitter.com/favorites/{$screen_name}.json";
  $tl = twitter_process($request);
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'User', $content);
}

function twitter_friends_page() {
  user_ensure_authenticated();
  $request = 'http://twitter.com/statuses/friends_timeline.json';
  $tl = twitter_process($request);
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Home', $content);
}

function theme_status_form($text = '') {
  if (user_is_authenticated()) {
    return "<form method='POST' action='update'><input name='status' value='{$text}'/> <input type='submit' value='Update' /></form>";
  }
}

function theme_status($status) {
  $time_since = theme('status_time_link', $status);
  $parsed = twitter_parse_tags($status->text);
  $avatar = theme('avatar', $status->user->profile_image_url, 1);

  $out = theme('status_form', "@{$status->user->screen_name} ");
  $out .= "<p>$parsed</p>
<table align='center'><tr><td>$avatar</td><td><a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a>
<br>$time_since</table>";
  if ($GLOBALS['user']['username'] == $status->user->screen_name) {
    $out .= "<form action='delete/{$status->id}' method='post'><input type='submit' value='Delete without confirmation' /></form>";
  }
  return $out;
}

function theme_user($feed) {
  $status = $feed[0];
  $out = theme('status_form', "@{$status->user->screen_name} ");
  $out .= "<table><tr><td>".theme('avatar', $status->user->profile_image_url, 1)."</td>
<td><b>{$status->user->screen_name}</b>
<br>{$status->user->description}
<br><a href='followers/{$status->user->screen_name}'>{$status->user->followers_count} followers</a>
| <a href='follow/{$status->user->screen_name}'>Follow</a> |
<a href='unfollow/{$status->user->screen_name}'>Unfollow</a>
</td></table>";
  $list = array();
  foreach ($feed as $status) {
    $list[] = twitter_parse_tags($status->text).' '.theme('status_time_link', $status);
  }
  $out .= theme('list', $list);
  return $out;
}

function theme_avatar($url, $force_large = false) {
  $size = $force_large ? 48 : 24;
  return "<img src='$url' height='$size' width='$size' />";
}

function theme_status_time_link($status) {
  $time_link = format_interval(time() - strtotime($status->created_at), 1);
  $source = $status->source ? " from {$status->source}" : '';
  return "<small><a href='status/{$status->id}'>$time_link ago</a>$source</small>";
}

function theme_directs($feed) {
  $rows = array();
  foreach ($feed as $status) {
    $text = twitter_parse_tags($status->text);
    $link = theme('status_time_link', $status);

    $rows[] = array(
      theme('avatar', $status->sender->profile_image_url),
      "<a href='user/{$status->sender->screen_name}'>{$status->sender->screen_name}</a> - {$link}<br>{$text}",
    );
  }
  return theme('table', array(), $rows, array('class' => 'timeline'));
}

function theme_timeline($feed) {
  $rows = array();
  if (count($feed) == 0) return theme('no_tweets');
  foreach ($feed as $status) {
    $text = twitter_parse_tags($status->text);
    $link = theme('status_time_link', $status);

    $rows[] = array(
      theme('avatar', $status->user->profile_image_url),
      "<a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a> - {$link}<br>{$text}",
    );
  }
  return theme('table', array(), $rows, array('class' => 'timeline'));
}

function theme_followers($feed) {
  $rows = array();
  if (count($feed) == 0) return '<p>No followers</p>';
  foreach ($feed as $user) {
    $rows[] = array(
      theme('avatar', $user->profile_image_url),
      "<a href='user/{$user->screen_name}'>{$user->screen_name}</a> - {$user->location}",
    );
  }
  return theme('table', array(), $rows, array('class' => 'followers'));
}

function theme_no_tweets() {
  return '<p>No tweets to display.</p>';
}

function theme_search_results($feed) {
  $rows = array();
  foreach ($feed->results as $status) {
    $text = twitter_parse_tags($status->text);
    $link = theme('status_time_link', $status);

    $rows[] = array(
      theme('avatar', $status->profile_image_url),
      "<a href='user/{$status->from_user}'>{$status->from_user}</a> - {$link}<br>{$text}",
    );
  }
  return theme('table', array(), $rows, array('class' => 'timeline'));
}

function theme_search_form() {
  return '<form action="search" method="GET"><input name="query" /><input type="submit" value="Search" /></form>';
}

function theme_external_link($url) {
  $encoded = urlencode($url);
  return "<a href='http://google.com/gwt/n?u={$encoded}'>{$url}</a>";
}

?>