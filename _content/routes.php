<?php

use MiniCms\Page;

return [
    '/' => new Page('pages/home', title: 'Home'),
    '/philosophy' => new Page('pages/philosophy', title: 'Philosophy'),
    '/framework' => new Page('pages/framework', title: 'The Framework'),
    '/views' => new Page('pages/views', title: 'Views & Widgets'),
    '/context' => new Page('pages/context', title: 'Context System'),
    '/storage' => new Page('pages/storage', title: 'Content Storage'),
    '/git' => new Page('pages/git', title: 'Git Workflow'),
];
