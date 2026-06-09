<?php $this->extend(); ?>
<?php $this->block('title', 'Content Storage — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'Content Storage', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p>All CMS content lives on the filesystem under <code>_content/</code>. There is no database for page content. This design makes content git-friendly, agent-friendly, and trivially portable.</p>
    ') ?>

    <h2>Directory layout</h2>
    <p>Each page context maps to a directory. Here is what a typical site looks like:</p>
    <pre><code>_content/
├── home/
│   ├── widgets.json          # text &amp; image values for the home page
│   ├── intro.html            # cms_html(&#39;intro&#39;, ...) content
│   ├── _feature-1/
│   │   ├── widgets.json      # heading text for Feature 1
│   │   └── body.html         # body HTML for Feature 1
│   ├── _feature-2/
│   │   ├── widgets.json
│   │   └── body.html
│   └── _feature-3/
│       ├── widgets.json
│       └── body.html
├── about/
│   ├── widgets.json
│   └── body.html
├── team/
│   └── erik/
│       └── widgets.json      # name, role, photo for /team/erik
├── routes.php                # page routing definitions
├── models.php                # entity registrations
└── site.json                 # site name and description</code></pre>

    <h2>widgets.json</h2>
    <p>Text and image values are stored as a flat JSON object keyed by slug:</p>
    <pre><code>{
    "heading": "About Our Company",
    "photo": "/uploads/team/photo.jpg"
}</code></pre>
    <p>The file is pretty-printed so diffs are clean and agents can edit values directly with standard JSON tools.</p>

    <h2>HTML files</h2>
    <p><code>cms_html()</code> content is stored in separate <code>.html</code> files named after the slug: <code>body.html</code>, <code>intro.html</code>, etc. This keeps rich content out of JSON (no escaping issues) and produces meaningful git diffs.</p>
    <p>An agent editing HTML content should write the file directly:</p>
    <pre><code># Read current content
cat _content/about/body.html

# Write updated content
cat &gt; _content/about/body.html &lt;&lt;&#39;HTML&#39;
&lt;p&gt;Updated paragraph with &lt;strong&gt;bold&lt;/strong&gt; text.&lt;/p&gt;
&lt;p&gt;A second paragraph.&lt;/p&gt;
HTML</code></pre>

    <h2>Editing content programmatically</h2>
    <p>The <code>ContentStore</code> class provides the read/write API:</p>
    <pre><code>$store = \mini\Mini::$mini-&gt;get(ContentStore::class);

// Text and image widgets
$value = $store-&gt;readWidget(&#39;home&#39;, &#39;heading&#39;);
$store-&gt;writeWidget(&#39;home&#39;, &#39;heading&#39;, &#39;New Title&#39;);

// HTML content
$html = $store-&gt;readHtml(&#39;home&#39;, &#39;intro&#39;);
$store-&gt;writeHtml(&#39;home&#39;, &#39;intro&#39;, &#39;&lt;p&gt;New content.&lt;/p&gt;&#39;);</code></pre>
    <p>But for agents, editing the files directly is usually simpler.</p>

    <h2>Path construction</h2>
    <p>Mini CMS uses <code>mini\Util\Path</code> for all filesystem path operations. Paths are joined and canonicalized — no manual string concatenation with <code>/</code>. This ensures correct behavior across platforms.</p>
</article>

<?php $this->end(); ?>
