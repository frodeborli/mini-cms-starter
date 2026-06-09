# Mini CMS Starter

A starter project for [Mini CMS](https://github.com/frodeborli/fubber-mini-cms) — a template-first CMS built as an aspect on the [Mini PHP framework](https://github.com/frodeborli/fubber-mini).

## Quick start

```bash
composer create-project fubber/mini-cms-starter my-site
cd my-site
php -S localhost:8899 -t html html/index.php
```

Open `http://localhost:8899` in your browser. Log in at `/login` to access the admin panel and inline editing.

## What's included

This starter ships a documentation site that doubles as a working example. It demonstrates:

- Page routing via `_content/routes.php`
- Template inheritance with `$this->extend()` and `$this->block()`
- Inline-editable content with `cms_text()`, `cms_html()`, and `cms_image()`
- Reusable partials with `cms_partial()`
- Context-aware content storage on the filesystem

## Project structure

```
_content/       Routes, models, site config, and content files (JSON/HTML)
_views/         PHP templates — override any CMS default file-by-file
_static/        Public static assets (CSS, images)
_routes/        Custom route handlers (filesystem-based routing)
html/           Web server document root
bootstrap.php   App bootstrap (loaded via Composer autoload)
```

## Making a new page

1. Add a route in `_content/routes.php`:
   ```php
   '/about' => new Page('pages/about', title: 'About'),
   ```

2. Create the view at `_views/pages/about.php`:
   ```php
   <?php $this->extend(); ?>
   <?php $this->block('title', 'About'); ?>
   <?php $this->block('content'); ?>
       <?= cms_text('heading', 'Heading', 1, 'About Us', 'h1') ?>
       <?= cms_html('body', 'Body', 2, '<p>Tell your story here.</p>') ?>
   <?php $this->end(); ?>
   ```

Content is stored as files in `_content/` — version it with git, edit it from the admin panel, or modify it directly on disk.

## Links

- [Mini CMS](https://github.com/frodeborli/fubber-mini-cms) — the CMS aspect
- [Mini framework](https://github.com/frodeborli/fubber-mini) — the underlying PHP framework
