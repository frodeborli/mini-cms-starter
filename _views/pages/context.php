<?php $this->extend(); ?>
<?php $this->block('title', 'Context System — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'The Context System', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p>Every widget needs a storage location. Instead of passing file paths to each widget call, Mini CMS uses a <em>context stack</em> that tracks where content should be read from and written to.</p>
    ') ?>

    <h2>How it works</h2>
    <p>When a page renders, Mini CMS sets a <strong>page context</strong> based on the URL:</p>
    <pre><code>/              → _content/home/
/about         → _content/about/
/team/erik     → _content/team/erik/</code></pre>
    <p>All <code>cms_text()</code>, <code>cms_html()</code>, and <code>cms_image()</code> calls within that page read and write to the page&rsquo;s context directory. The slug parameter becomes the key in <code>widgets.json</code> or the filename for <code>.html</code> content.</p>

    <h2>cms_partial() — reusable sub-contexts</h2>
    <p>The most common way to create a nested context. It renders a view template inside its own content scope:</p>
    <pre><code>&lt;?= cms_partial(&#39;partials/card.php&#39;, &#39;Feature 1&#39;) ?&gt;
&lt;?= cms_partial(&#39;partials/card.php&#39;, &#39;Feature 2&#39;) ?&gt;
&lt;?= cms_partial(&#39;partials/card.php&#39;, &#39;Feature 3&#39;) ?&gt;</code></pre>
    <p>Each call pushes a sub-context named after the label. The label is slugified and prefixed with <code>_</code> to avoid collision with URL-derived paths:</p>
    <pre><code>&#39;Feature 1&#39; → _content/home/_feature-1/
&#39;Feature 2&#39; → _content/home/_feature-2/
&#39;Feature 3&#39; → _content/home/_feature-3/</code></pre>
    <p>Inside <code>partials/card.php</code>, widget slugs like <code>&#39;heading&#39;</code> and <code>&#39;body&#39;</code> are scoped to that sub-context. No conflicts even though the same template runs three times with identical slug names.</p>

    <h2>Manual context control</h2>
    <p>For cases where <code>cms_partial()</code> doesn&rsquo;t fit, use the explicit push/pop functions:</p>
    <pre><code>&lt;?php cms_context_start(&#39;sidebar&#39;, &#39;Sidebar&#39;); ?&gt;
    &lt;?= cms_text(&#39;cta&#39;, &#39;Call to Action&#39;, 1, &#39;Contact us&#39;, &#39;h3&#39;) ?&gt;
    &lt;?= cms_html(&#39;cta-body&#39;, &#39;CTA Body&#39;, 2, &#39;&lt;p&gt;Get in touch.&lt;/p&gt;&#39;) ?&gt;
&lt;?php cms_context_end(); ?&gt;</code></pre>
    <p>This creates <code>_content/home/_sidebar/</code>. Every <code>cms_context_start()</code> must have a matching <code>cms_context_end()</code> — Mini CMS throws a <code>LogicException</code> if the stack is unbalanced after rendering.</p>

    <h2>Why the underscore prefix?</h2>
    <p>Page URLs map to directory names directly: <code>/about</code> stores content in <code>_content/about/</code>. If a sub-context were also named <code>about</code>, its directory would collide with the page directory. The <code>_</code> prefix on sub-context directories (<code>_feature-1</code>, <code>_sidebar</code>) prevents this — URL paths never start with an underscore.</p>

    <h2>Absolute context paths</h2>
    <p>Context paths starting with <code>/</code> resolve from the content root instead of nesting under the current context. This is useful for shared content that appears on multiple pages:</p>
    <pre><code>&lt;?php cms_context_start(&#39;/shared/footer-cta&#39;, &#39;Footer CTA&#39;); ?&gt;
    &lt;?= cms_text(&#39;heading&#39;, &#39;Heading&#39;, 1, &#39;Ready to start?&#39;, &#39;h2&#39;) ?&gt;
&lt;?php cms_context_end(); ?&gt;</code></pre>
    <p>This reads from <code>_content/_shared/footer-cta/</code> regardless of which page is rendering.</p>

    <h2>Admin UI grouping</h2>
    <p>In the admin panel, widgets are grouped by their context label. The home page example above shows three groups: &ldquo;Page&rdquo; (the base context), &ldquo;Feature 1&rdquo;, &ldquo;Feature 2&rdquo;, and &ldquo;Feature 3&rdquo; — each containing its own &ldquo;heading&rdquo; and &ldquo;body&rdquo; fields.</p>
</article>

<?php $this->end(); ?>
