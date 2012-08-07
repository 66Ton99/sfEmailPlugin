<?php
if ($content instanceof sfOutputEscaper) {
  echo $content->getRawValue();
} elseif (method_exists('sfOutputEscaper', 'unescape')) {
  echo sfOutputEscaper::unescape($content);
}