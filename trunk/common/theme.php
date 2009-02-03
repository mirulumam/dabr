<?php

$current_theme = false;

function theme() {
  global $current_theme;
  $args = func_get_args();
  $function = array_shift($args);
  $function = 'theme_'.$function;
  
  if ($current_theme) {
    $custom_function = $current_theme.'_'.$function;
    if (function_exists($custom_function))
      $function = $custom_function;
  } else {
    if (!function_exists($function))
      return "<p>Error: theme function <b>$function</b> not found.</p>";
  }
  return call_user_func_array($function, $args);
}

function theme_csv($headers, $rows) {
  $out = implode(',', $headers)."\n";
  foreach ($rows as $row) {
    $out .= implode(',', $row)."\n";
  }
  return $out;
}

function theme_list($items, $attributes) {
  if (!is_array($items) || count($items) == 0) {
    return '';
  }
  $output = '<ul'.theme_attributes($attributes).'>';
  foreach ($items as $item) {
    $output .= "<li>$item</li>\n";
  }
  $output .= "</ul>\n";
  return $output;
}

function theme_options($options, $selected = NULL) {
  if (count($options) == 0) return '';
  $output = '';
  foreach($options as $value => $name) {
    if (is_array($name)) {
      $output .= '<optgroup label="'.$value.'">';
      $output .= theme('options', $name, $selected);
      $output .= '</optgroup>';
    } else {
      $output .= '<option value="'.$value.'"'.($selected == $value ? ' selected="selected"' : '').'>'.$name."</option>\n";
    }
  }
  return $output;
}

function theme_info($info) {
  $rows = array();
  foreach ($info as $name => $value) {
    $rows[] = array($name, $value);
  }
  return theme('table', array(), $rows);
}

function theme_table($headers, $rows, $attributes = NULL) {
  $out = '<table'.theme_attributes($attributes).'>';
  if (count($headers) > 0) {
    $out .= '<thead><tr>';
    foreach ($headers as $cell) {
      $out .= theme_table_cell($cell, TRUE);
    }
    $out .= '</tr></thead>';
  }
  if (count($rows) > 0) {
    $out .= '<tbody>'.theme('table_rows', $rows).'</tbody>';
  }
  $out .= '</table>';
  return $out;
}

function theme_table_rows($rows) {
  $i = 0;
  foreach ($rows as $row) {
    if ($row['data']) {
      $cells = $row['data'];
      unset($row['data']);
      $attributes = $row;
    } else {
      $cells = $row;
      $attributes = FALSE;
    }
    $attributes['class'] .= ($attributes['class'] ? ' ' : '') . ($i++ %2 ? 'even' : 'odd');
    $out .= '<tr'.theme_attributes($attributes).'>';
    foreach ($cells as $cell) {
      $out .= theme_table_cell($cell);
    }
    $out .= "</tr>\n";
  }
  return $out;
}

function theme_attributes($attributes) {
  if (!$attributes) return;
  foreach ($attributes as $name => $value) {
    $out .= " $name=\"$value\"";
  }
  return $out;
}

function theme_table_cell($contents, $header = FALSE) {
  $celltype = $header ? 'th' : 'td';
  if (is_array($contents)) {
    $value = $contents['data'];
    unset($contents['data']);
    $attributes = $contents;
  } else {
    $value = $contents;
    $attributes = false;
  }
  return "<$celltype".theme_attributes($attributes).">$value</$celltype>";
}


function theme_error($message) {
  theme_page('Error', $message);
}

function theme_page($title, $content) {
  $body = theme('menu_top');
  $body .= $content;
  $body .= theme('menu_bottom');
  ob_start('ob_gzhandler');
  header('Content-Type: text/html; charset=utf-8');
  echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>',$_SERVER['SERVER_NAME'],' - ',$title,'</title><base href="',BASE_URL,'" />
'.theme('css').'
<body>', $body, '</body>
</html>';
  exit();
}

function theme_css() {
  return '<style type="text/css">a{color:#b50}table{border-collapse:collapse}form{margin:.3em;}td{vertical-align:top;padding:0.3em}img{border:0}small,small a{color:#555}body{background:#ddd;color:#111;margin:0;font:90% sans-serif}tr.odd td{background:#fff}tr.even td{background:#eee}tr.reply td{background:#FFA}tr.reply.even td{background: #DD9}.menu{color:#c40;background:#e81;padding: 2px}.menu a{color:#fff;text-decoration: none}
</style>';
}

?>