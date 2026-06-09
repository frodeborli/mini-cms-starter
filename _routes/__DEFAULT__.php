<?php

$content = \mini\Mini::$mini->get(\MiniCms\Content::class);
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$page = $content->resolve($path);

if ($page === null) {
    throw new \mini\Exceptions\NotFoundException("Page not found: $path");
}

return $page;
