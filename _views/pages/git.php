<?php $this->extend(); ?>
<?php $this->block('title', 'Git Workflow — Mini CMS'); ?>
<?php $this->block('content'); ?>

<article>
    <?= cms_text('heading', 'Heading', 1, 'Git Workflow', 'h1') ?>
    <?= cms_html('intro', 'Introduction', 2, '
        <p>Mini CMS stores all content as files. This means git is your versioning system, your backup, and your undo button. It also means a careless <code>git reset --hard</code> or <code>git checkout .</code> can destroy every edit the site owner has made. Handle with care.</p>
    ') ?>

    <h2>What is tracked in git</h2>
    <p>A Mini CMS project has two kinds of files that both belong in the repo:</p>
    <table class="param-table">
        <tr><td><strong>Code</strong></td><td>Views (<code>_views/</code>), routes (<code>_content/routes.php</code>), models, PHP classes, CSS, JS</td></tr>
        <tr><td><strong>Content</strong></td><td><code>_content/</code> directories: <code>widgets.json</code> files, <code>.html</code> files, <code>site.json</code></td></tr>
    </table>
    <p>Both should be committed. Content files <em>are</em> the site&rsquo;s data — they are the equivalent of a database, except they live in the repo.</p>

    <h2>Rules for coding agents</h2>

    <h3>1. Never discard content changes</h3>
    <p>Before any destructive git operation, check if <code>_content/</code> has uncommitted changes:</p>
    <pre><code>git status _content/</code></pre>
    <p>If it does, commit those changes first. Content edits come from the site admin using the inline editor — they are user data, not code artifacts.</p>

    <h3>2. Commit content and code separately</h3>
    <p>Keep content commits separate from code changes when possible:</p>
    <pre><code># Commit content that the admin has edited
git add _content/
git commit -m "Content update: home page and about page"

# Then commit your code changes
git add _views/ _static/ src/
git commit -m "Add new feature card partial"</code></pre>
    <p>This makes history readable and reverts safe — you can roll back a code change without losing content.</p>

    <h3>3. Never use these commands without thinking</h3>
    <pre><code># DANGEROUS — destroys all uncommitted content edits
git checkout .
git reset --hard
git clean -fd

# SAFER alternatives
git stash                    # saves everything, recoverable
git diff _content/           # inspect before deciding
git checkout -- _views/      # discard only code changes</code></pre>

    <h3>4. Pull before pushing</h3>
    <p>If the site admin has been editing content while you work on code, their changes may be on the server but not in your working tree. Always pull and handle merges carefully:</p>
    <pre><code>git pull --rebase</code></pre>
    <p>Content files rarely conflict (different pages edit different files), but if they do, the admin&rsquo;s version is almost always correct.</p>

    <h3>5. Use branches for structural changes</h3>
    <p>When changing view templates or route structure, work on a branch. If the changes affect which widgets exist or how content is organized, the branch can carry the migrated content files alongside the code changes:</p>
    <pre><code>git checkout -b feature/new-layout
# Edit views, move content files, test
git add .
git commit -m "Restructure home page layout"
git checkout main
git merge feature/new-layout</code></pre>

    <h2>Uploads</h2>
    <p>The <code>uploads/</code> directory contains media files managed by the CMS media library. Whether to track these in git depends on the project:</p>
    <ul>
        <li><strong>Small sites:</strong> commit uploads. Simple, complete backups, works for deployment.</li>
        <li><strong>Large sites:</strong> add <code>uploads/</code> to <code>.gitignore</code> and handle media separately (rsync, object storage, etc.).</li>
    </ul>

    <h2>The key insight</h2>
    <p>In a traditional CMS, the database is a black box that git never sees. In Mini CMS, the database <em>is</em> the filesystem, and git sees everything. This is a feature: free versioning, free backups, free diff. But it means you must treat <code>_content/</code> with the same respect you would give a production database.</p>
</article>

<?php $this->end(); ?>
