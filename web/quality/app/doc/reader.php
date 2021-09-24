<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
if (null == $file = $request->query->get('src', null)) {
    $file = 'README.md';
}

// parse only inline elements (useful for one-line descriptions)
$parser     = new \cebe\markdown\GithubMarkdown();
if (file_exists(__DIR__ . '/../../' . $file)) {
    $mdParsed = $parser->parse(file_get_contents(__DIR__ . '/../../' . $file));
}
elseif (file_exists(__DIR__ . '/' . $file)) {
    $mdParsed = $parser->parse(file_get_contents(__DIR__ . '/' . $file));
}

$replace    ='$1'. $request->getScheme() . '://' . $request->getHost() . $request->getBasePath() . '/reader.php/?src=$2$3';
$pattern    ="@(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!#)(?!http)([^\"'>]+)([\"'>]+)@";

echo preg_replace($pattern,$replace, $mdParsed);