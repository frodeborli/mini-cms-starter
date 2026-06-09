<?php
$guideUrls = ['/views', '/context', '/storage', '/git'];
$topItems = [];
$guideItems = [];
$guideActive = false;
foreach ($nav as $item) {
    if (in_array($item['url'], $guideUrls)) {
        $guideItems[] = $item;
        if ($item['active'] ?? false) $guideActive = true;
    } else {
        $topItems[] = $item;
    }
}
?>
<header>
    <nav>
        <a href="/" class="site-name"><?= \mini\h($siteName) ?></a>
        <button class="nav-toggle" aria-label="Menu" onclick="this.closest('nav').classList.toggle('open')">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-links">
            <?php foreach ($topItems as $item): ?>
            <?php if ($item['url'] === '/') continue; ?>
            <li><a href="<?= \mini\h($item['url']) ?>"<?= ($item['active'] ?? false) ? ' class="active"' : '' ?>><?= \mini\h($item['title']) ?></a></li>
            <?php endforeach; ?>
            <?php if ($guideItems): ?>
            <li class="dropdown" onclick="this.classList.toggle('open'); event.stopPropagation()">
                <a href="#" class="dropdown-toggle<?= $guideActive ? ' active' : '' ?>" onclick="event.preventDefault()">Guide <span class="caret">&#9662;</span></a>
                <ul class="dropdown-menu">
                    <?php foreach ($guideItems as $item): ?>
                    <li><a href="<?= \mini\h($item['url']) ?>"<?= ($item['active'] ?? false) ? ' class="active"' : '' ?>><?= \mini\h($item['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
