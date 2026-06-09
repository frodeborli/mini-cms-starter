<?php $this->extend(); ?>
<?php $this->block('title', 'Views & Widgets — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'Views & Widgets', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p>CMS content is defined inline in view templates using three widget functions. Each call declares an editable region: the developer controls where it appears and what type it is; the admin controls the value.</p>
    ') ?>

    <h2>cms_text()</h2>
    <p>Plain text, HTML-escaped on output. Optionally wrapped in a tag.</p>
    <pre><code>&lt;?= cms_text(&#39;heading&#39;, &#39;Page Heading&#39;, 1, &#39;Default Title&#39;, &#39;h1&#39;) ?&gt;</code></pre>
    <table class="param-table">
        <tr><td><code>$slug</code></td><td>Unique identifier within the current context. Stored as a key in <code>widgets.json</code>.</td></tr>
        <tr><td><code>$label</code></td><td>Human-readable name shown in the admin sidebar.</td></tr>
        <tr><td><code>$pos</code></td><td>Integer controlling field order in the admin UI.</td></tr>
        <tr><td><code>$default</code></td><td>Fallback value when no content has been saved.</td></tr>
        <tr><td><code>$tag</code></td><td>CSS-selector-style wrapper tag. <code>&#39;h1.hero#main&#39;</code> renders <code>&lt;h1 class="hero" id="main"&gt;</code>. Empty string = no wrapper.</td></tr>
    </table>
    <p>The <code>->plain()</code> method returns the escaped value without a wrapper, useful in <code>&lt;title&gt;</code> tags:</p>
    <pre><code>&lt;?php $this->block(&#39;title&#39;, cms_text(&#39;heading&#39;, &#39;Heading&#39;, 1, &#39;Home&#39;)-&gt;plain()); ?&gt;</code></pre>

    <h2>cms_html()</h2>
    <p>Rich HTML content. Stored in a separate <code>.html</code> file (not JSON), making it easy for agents to edit and producing clean git diffs.</p>
    <pre><code>&lt;?= cms_html(&#39;body&#39;, &#39;Body Content&#39;, 2, &#39;&lt;p&gt;Default paragraph.&lt;/p&gt;&#39;) ?&gt;</code></pre>
    <p>The parameters are identical to <code>cms_text()</code>. In preview mode, the element gets <code>contenteditable</code> with a formatting toolbar.</p>

    <h2>cms_image()</h2>
    <p>Responsive image with optional aspect ratio cropping and srcset generation.</p>
    <pre><code>&lt;?= cms_image(&#39;photo&#39;, &#39;Hero Photo&#39;, 1, &#39;&#39;, &#39;&#39;, &#39;A landscape photo&#39;, &#39;16x9&#39;) ?&gt;</code></pre>
    <table class="param-table">
        <tr><td><code>$slug</code></td><td>Identifier, same as text/html widgets.</td></tr>
        <tr><td><code>$label</code></td><td>Admin UI label.</td></tr>
        <tr><td><code>$pos</code></td><td>Sort order.</td></tr>
        <tr><td><code>$default</code></td><td>Default image URL (empty = placeholder shown).</td></tr>
        <tr><td><code>$tag</code></td><td>Wrapper tag selector.</td></tr>
        <tr><td><code>$alt</code></td><td>Alt text for the <code>&lt;img&gt;</code>.</td></tr>
        <tr><td><code>$aspect</code></td><td>Aspect ratio for cropping, e.g. <code>&#39;16x9&#39;</code>, <code>&#39;3x4&#39;</code>, <code>&#39;1x1&#39;</code>.</td></tr>
    </table>

    <h2>Tag selector syntax</h2>
    <p>The <code>$tag</code> parameter uses a CSS-selector-like syntax parsed into an HTML tag:</p>
    <pre><code>&#39;h1&#39;             → &lt;h1&gt;...&lt;/h1&gt;
&#39;h1.blue&#39;        → &lt;h1 class="blue"&gt;...&lt;/h1&gt;
&#39;div#hero.large&#39; → &lt;div id="hero" class="large"&gt;...&lt;/div&gt;
&#39;p.lead.italic&#39;  → &lt;p class="lead italic"&gt;...&lt;/p&gt;
&#39;&#39;               → no wrapper (content rendered bare)</code></pre>
    <p>Tags, classes, and IDs can appear in any order: <code>&#39;h1.blue#main.caps&#39;</code> works the same as <code>&#39;h1#main.blue.caps&#39;</code>.</p>
</article>

<?php $this->end(); ?>
