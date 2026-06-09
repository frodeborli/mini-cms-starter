<?php $this->extend(); ?>
<?php $this->block('title', 'Mini CMS — Template-first CMS for Mini'); ?>
<?php $this->block('content'); ?>

<section class="hero">
    <?= cms_text('heading', 'Heading', 1, 'Mini CMS', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '<p class="lead">A template-first CMS built as an aspect on the Mini PHP framework. Inline editing, media library, auto CRUD, and an admin panel — layered on a plain Mini app without changing how the app works.</p>') ?>
</section>

<section class="features">
    <div class="feature">
        <?= cms_partial('partials/feature.php', 'Feature 1', ['defaultHeading' => 'Template-first', 'defaultBody' => '<p>Content lives inside your PHP templates as <code>cms_text()</code>, <code>cms_html()</code>, and <code>cms_image()</code> calls. The developer defines the structure; the admin edits the values. No database schemas, no content types to configure.</p>']) ?>
    </div>
    <div class="feature">
        <?= cms_partial('partials/feature.php', 'Feature 2', ['defaultHeading' => 'Filesystem storage', 'defaultBody' => '<p>Content is stored as JSON and HTML files under <code>_content/</code>. This means <code>git diff</code> shows exactly what changed, branches can carry different content, and a coding agent can edit content directly on disk.</p>']) ?>
    </div>
    <div class="feature">
        <?= cms_partial('partials/feature.php', 'Feature 3', ['defaultHeading' => 'Context-aware', 'defaultBody' => '<p>Reusable partials get their own content scope automatically. Embed the same card template three times on a page — each instance stores its content independently, no slug collisions.</p>']) ?>
    </div>
</section>

<section class="doc-section">
    <h2>Quick start</h2>
    <pre><code>composer create-project fubber/mini-cms-starter my-site
cd my-site
mini serve</code></pre>
    <p>Log in at <code>/login</code> with the default credentials. Every page is inline-editable: long-press (or click Edit in the sidebar) to activate edit mode, make changes, then Save.</p>
</section>

<section class="doc-section">
    <h2>For coding agents</h2>
    <p>This documentation site is itself a Mini CMS site. The pages you see here are the default starter content. An AI coding agent working on a Mini CMS project should:</p>
    <ol>
        <li>Read the view templates in <code>_views/</code> to understand page structure</li>
        <li>Read <code>_content/routes.php</code> to see all registered pages</li>
        <li>Edit <code>_content/</code> JSON and HTML files to modify content directly</li>
        <li>Use <code>git</code> carefully — content files are versioned, and a bad reset destroys real data</li>
    </ol>
    <p>See the <a href="/git">Git Workflow</a> page for detailed guidance.</p>
</section>

<?php $this->end(); ?>
