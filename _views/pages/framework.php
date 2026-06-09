<?php $this->extend(); ?>
<?php $this->block('title', 'The Framework — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'The Framework', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p class="lead">Mini is a full-featured PHP framework with zero non-PSR dependencies. The CMS is built on it, not instead of it. Understanding the framework is essential for building anything beyond a static brochure site.</p>
    ') ?>

    <h2>Project structure</h2>
    <pre><code>my-site/
├── html/
│   └── index.php           # Entry point: require autoload, mini\dispatch()
├── _routes/                # Filesystem-based routing
│   └── __DEFAULT__.php     # CMS catch-all route handler
├── _views/                 # Templates (override aspect defaults)
│   ├── _layout.php         # (provided by CMS aspect, overridable)
│   └── pages/
├── _content/               # CMS content, routes, models, site config
│   ├── routes.php
│   ├── models.php
│   └── site.json
├── _static/                # Public static assets
│   └── style.css
├── _config/                # Service configuration (per-FQCN files)
├── _migrations/            # Database migrations
├── src/                    # Application PHP classes
├── uploads/                # CMS media library
├── bootstrap.php           # App bootstrap (autoload.files)
├── composer.json
└── vendor/</code></pre>

    <h2>Routing</h2>
    <p>Mini uses filesystem-based routing. Files in <code>_routes/</code> map directly to URL paths:</p>
    <pre><code>_routes/api/ping.php     → GET /api/ping
_routes/api/users/_.php  → GET /api/users/{id}
_routes/blog/_/          → GET /blog/{slug}/...
_routes/__DEFAULT__.php  → catch-all for unmatched paths</code></pre>
    <p>Wildcards: <code>_.php</code> matches a single segment, <code>_/</code> matches a directory segment. Captured values are available as <code>$_GET[0]</code>, <code>$_GET[1]</code>, etc.</p>

    <h3>Route return values</h3>
    <p>A route file returns a value that Mini dispatches:</p>
    <pre><code>// Return a PSR-7 response directly
return new \mini\Http\Message\JsonResponse(['status' =&gt; 'ok']);

// Return a ResponseAggregate (lazy response)
return new MyPage($slug);  // must implement getResponse()

// Return a closure (typed parameter injection)
return function(int $_0, OrderService $svc) {
    return $svc-&gt;getOrder($_0);
};

// Return a controller (sub-router for a subtree)
return new ApiController();</code></pre>

    <h3>CMS page routing</h3>
    <p>CMS pages are registered in <code>_content/routes.php</code>, not as route files. The catch-all <code>_routes/__DEFAULT__.php</code> resolves them:</p>
    <pre><code>// _content/routes.php
return [
    '/'        =&gt; new Page('pages/home', title: 'Home'),
    '/about'   =&gt; new Page('pages/about', title: 'About'),
    '/blog/{slug}' =&gt; new Page('blog/post', title: 'Blog Post'),
];</code></pre>
    <p>You can freely mix CMS pages and custom route files. A route file at <code>_routes/api/data.php</code> takes priority over any CMS page at <code>/api/data</code>.</p>

    <h2>Templates</h2>
    <p>Pure PHP templates with multi-level inheritance modeled after .NET Core Razor Pages:</p>
    <pre><code>// pages/about.php
&lt;?php $this-&gt;extend(); ?&gt;
&lt;?php $this-&gt;block('title', 'About Us'); ?&gt;
&lt;?php $this-&gt;block('content'); ?&gt;
    &lt;h1&gt;About Us&lt;/h1&gt;
    &lt;p&gt;Content here.&lt;/p&gt;
&lt;?php $this-&gt;end(); ?&gt;</code></pre>
    <p><code>$this-&gt;extend()</code> with no argument walks up the directory tree looking for <code>_layout.php</code>. Blocks defined with <code>$this-&gt;block()</code> are filled into the parent via <code>$this-&gt;show()</code>. Multi-level inheritance (child &rarr; section layout &rarr; base layout) works correctly.</p>

    <h2>Database</h2>
    <p>Mini is SQL-first. The primary tool is <code>mini\Database\PartialQuery</code> &mdash; an immutable query builder that doubles as an access-control primitive:</p>
    <pre><code>// Views get a query() helper that returns a PartialQuery
&lt;?php foreach (query("SELECT * FROM posts WHERE published = 1") as $post): ?&gt;
    &lt;h2&gt;&lt;?= \mini\h($post-&gt;title) ?&gt;&lt;/h2&gt;
&lt;?php endforeach; ?&gt;</code></pre>
    <p>Because <code>PartialQuery</code> is immutable, passing one into a template is safe &mdash; the template can add <code>WHERE</code> clauses to narrow results but can never widen them. This makes it a natural authorization boundary.</p>

    <h3>Models</h3>
    <p>Active Record models with two-tier methods:</p>
    <pre><code>class Post extends Model {
    #[Table] public static string $table = 'posts';
    #[PrimaryKey] public int $id;
    #[Required] #[MaxLength(200)] public string $title;
    #[Required] public string $body;
    #[CreatedAt] public string $created_at;
}

// Auth-checked (respects authorization rules)
$posts = Post::query()-&gt;where('published', 1);
$post-&gt;save();

// Unchecked (bypasses auth, use for system operations)
$posts = Post::queryUnsafe();
$post-&gt;saveUnsafe();</code></pre>
    <p>To expose a model in the CMS admin panel, register it in <code>_content/models.php</code> as an Entity. The CMS auto-generates index, create, edit, and show views.</p>

    <h2>Services</h2>
    <p>Mini has a PSR-11 container with convenience functions:</p>
    <pre><code>// Typed helpers (plain functions in the mini namespace)
$db = \mini\db();          // DatabaseInterface
$cache = \mini\cache();    // CacheInterface
$auth = \mini\auth();      // AuthInterface
$mailer = \mini\mailer();  // MailerInterface
$req = \mini\request();    // ServerRequestInterface

// Direct container access
$service = \mini\Mini::$mini-&gt;get(MyService::class);</code></pre>
    <p>Register services in <code>bootstrap.php</code>:</p>
    <pre><code>Mini::$mini-&gt;addService(MyService::class, Lifetime::Singleton, function() {
    return new MyService(Mini::$mini-&gt;root . '/data');
});</code></pre>

    <h2>What else is built in</h2>
    <p>Mini is more complete than it looks. Before reaching for <code>composer require</code>, check whether Mini already provides the feature:</p>
    <ul>
        <li><strong>Hooks</strong> &mdash; typed event dispatchers: <code>Event</code>, <code>Trigger</code>, <code>Handler</code>, <code>Filter</code>, <code>StateMachine</code></li>
        <li><strong>Validation</strong> &mdash; JSON Schema-compatible validators with PHP attributes</li>
        <li><strong>Mail</strong> &mdash; full RFC 5322 implementation with streaming MIME (not a Symfony Mailer wrapper)</li>
        <li><strong>HTTP client</strong> &mdash; PSR-18 implementation</li>
        <li><strong>Cache</strong> &mdash; PSR-16 implementation</li>
        <li><strong>Virtual database</strong> &mdash; federated SQL engine with SQL:2003 coverage (CTEs, window functions, joins across heterogeneous sources)</li>
        <li><strong>i18n</strong> &mdash; ICU MessageFormat via <code>t("Hello {name}", [...])</code></li>
        <li><strong>Authorization</strong> &mdash; capability-based, chain-of-responsibility, with field-level granularity</li>
        <li><strong>Path utilities</strong> &mdash; cross-platform path manipulation (<code>mini\Util\Path</code>)</li>
    </ul>
</article>

<?php $this->end(); ?>
