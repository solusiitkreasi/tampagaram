<?php

use App\Models\Page;
use App\Models\PageContent;

if (!function_exists('convertUtf8')) {
  function convertUtf8($value)
  {
    return mb_detect_encoding($value, mb_detect_order(), true) === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8');
  }
}

if (!function_exists('createSlug')) {
  function createSlug($string)
  {
    $slug = preg_replace('/\s+/u', '-', trim($string));
    $slug = str_replace('/', '', $slug);
    $slug = str_replace('?', '', $slug);
    $slug = str_replace(',', '', $slug);

    return mb_strtolower($slug, 'UTF-8');
  }
}

if (!function_exists('hex2rgb')) {
  function hex2rgb($colour)
  {
    if ($colour[0] == '#') {
      $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
      list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } elseif (strlen($colour) == 3) {
      list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    } else {
      return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return array('red' => $r, 'green' => $g, 'blue' => $b);
  }
}

if (!function_exists('replaceBaseUrl')) {
  function replaceBaseUrl($html, $type)
  {
    $startDelimiter = 'src=""';
    if ($type == 'summernote') {
      $endDelimiter = '/assets/img/summernote';
    } elseif ($type == 'pagebuilder') {
      $endDelimiter = '/assets/img';
    }

    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;

    while (false !== ($contentStart = strpos($html, $startDelimiter, $startFrom))) {
      $contentStart += $startDelimiterLength;
      $contentEnd = strpos($html, $endDelimiter, $contentStart);

      if (false === $contentEnd) {
        break;
      }

      $html = substr_replace($html, url('/'), $contentStart, $contentEnd - $contentStart);
      $startFrom = $contentEnd + $endDelimiterLength;
    }

    return $html;
  }
}

if (!function_exists('setEnvironmentValue')) {
  function setEnvironmentValue(array $values)
  {
    $envFile = app()->environmentFilePath();
    $str = file_get_contents($envFile);

    if (count($values) > 0) {
      foreach ($values as $envKey => $envValue) {
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, "\n", $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

        // If key does not exist, add it
        if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
          $str .= "{$envKey}={$envValue}\n";
        } else {
          $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        }
      }
    }

    $str = substr($str, 0, -1);

    if (!file_put_contents($envFile, $str)) return false;

    return true;
  }
}

if (!function_exists('getHref')) {
  function getHref($link, $langid)
  {
    $href = "#";

    if ($link["type"] == 'home') {
      $href = route('index');
    } else if ($link["type"] == 'rooms') {
      $href = route('rooms');
    } else if ($link["type"] == 'services') {
      $href = route('services');
    } else if ($link["type"] == 'blogs') {
      $href = route('blogs');
    } else if ($link["type"] == 'gallery') {
      $href = route('gallery');
    } else if ($link["type"] == 'packages') {
      $href = route('packages');
    } else if ($link["type"] == 'faq') {
      $href = route('faqs');
    } else if ($link["type"] == 'contact') {
      $href = route('contact');
    } else if ($link["type"] == 'custom') {
      if (empty($link["href"])) {
        $href = "#";
      } else {
        $href = $link["href"];
      }
    } else {
      $pageid = (int)$link["type"];
      $page = PageContent::where('page_id', $pageid)->where('language_id', $langid)->first();
      if (!empty($page)) {
        $href = route('front.dynamicPage', [$page->slug]);
      } else {
        $href = '#';
      }
    }

    return $href;
  }
}
