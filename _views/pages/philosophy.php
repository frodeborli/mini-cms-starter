<?php $this->extend(); ?>
<?php $this->block('title', 'Philosophy — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'Philosophy', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p class="lead">Mini CMS is not a standalone product. It is a CMS <em>aspect</em> on top of the Mini PHP framework &mdash; a full application framework with zero non-PSR dependencies that provides routing, templating, database, auth, validation, mail, caching, and more out of the box.</p>
    ') ?>

    <h2>The framework comes first</h2>
    <p>You start by building a normal Mini application: routes, views, models, business logic. The CMS is layered on top without changing how the application works. Your route files, controllers, and templates are plain PHP &mdash; there is no CMS abstraction between you and the framework.</p>
    <p>This means a Mini CMS site is a real application, not a theme. You can have custom routes with complex logic, database models with authorization rules, API endpoints, background jobs &mdash; anything the framework supports. The CMS just adds inline editing to the parts you choose.</p>

    <h2>Aspects, not plugins</h2>
    <p>Mini organizes features as <strong>aspects</strong> &mdash; Composer packages that contribute routes, views, static assets, config, and migrations via path-registry overlays. The CMS is one such aspect (<code>fubber/mini-cms</code>). It ships defaults for the admin panel, inline editing JS, media library, and CRUD views. Your application can override any of these file-by-file.</p>
    <p>This overlay system means the CMS never forces you into its UI patterns. If the default admin shell doesn&rsquo;t fit, override <code>_views/cms/admin-shell.php</code> in your app. If you need a custom entity form, point the entity at your own view. The framework&rsquo;s file resolution does the rest.</p>

    <h2>Zero external dependencies</h2>
    <p>Mini implements PSR-7, PSR-11, PSR-17, PSR-18, and PSR-16 from scratch. No Symfony components, no Guzzle, no League packages. The CMS follows the same principle: all vendor JS and CSS (Bootstrap, AdminLTE, Cropper.js) is served locally from <code>_static/admin/vendor/</code>. No external CDNs, no tracking scripts, no cookies from third-party domains.</p>
    <p>This is a privacy requirement. A default CMS installation must not leak visitor data to external services.</p>

    <h2>Filesystem as database</h2>
    <p>CMS content is stored as JSON and HTML files, not in a database. This is a deliberate choice:</p>
    <ul>
        <li><strong>Git versioning</strong> &mdash; every content edit is a file change that <code>git diff</code> can show and <code>git revert</code> can undo</li>
        <li><strong>Agent-friendly</strong> &mdash; an AI coding agent can read and write content files directly, no API needed</li>
        <li><strong>Portable</strong> &mdash; copy the directory and you have the entire site, no database export/import</li>
        <li><strong>Mergeable</strong> &mdash; content changes on different pages touch different files, so branches merge cleanly</li>
    </ul>
    <p>The database is still there for structured data (models, entities, anything that needs querying). But page content &mdash; headings, paragraphs, images &mdash; belongs on the filesystem where tools can reach it.</p>

    <h2>Template-first, not schema-first</h2>
    <p>Traditional CMSes require you to define content types, fields, and schemas before you can build a page. Mini CMS inverts this: you write a template with <code>cms_text()</code> and <code>cms_html()</code> calls inline, and the CMS discovers the editable fields by rendering the page. No configuration step, no admin panel setup, no migration.</p>
    <p>The template <em>is</em> the schema. Add a widget call to a view and it appears in the editor. Remove it and it disappears. Rename it and the old content stays on disk (harmlessly) while the new name starts fresh.</p>

    <h2>Don&rsquo;t work around the framework</h2>
    <p>If something in Mini doesn&rsquo;t work as expected, that&rsquo;s a framework bug to discuss and fix &mdash; not something to patch around in the CMS. This keeps the CMS thin and the framework honest. The CMS should never contain code that compensates for missing or broken framework features.</p>
</article>

<?php $this->end(); ?>
