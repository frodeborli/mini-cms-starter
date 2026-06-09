# MiniCMS — Project Guide

This is a template-first CMS built as an aspect on the Mini PHP framework. The CMS adds inline editing, a media library, auto CRUD for database models, and an admin panel — all layered on top of a plain Mini application without changing how the app itself works.

Mini's own `CLAUDE.md` lives in `vendor/fubber/mini/CLAUDE.md`. Read it before working on framework-level code. This file covers the CMS layer and the demo site that ships with it.

## Project layout

The root application is a demo site (astridselvik.no). The CMS lives in `aspects/cms/` as a reusable aspect.

```
_content/           Site content — routes.php, models.php, site.json, and JSON content files
_views/             Site-specific views (override anything the CMS aspect provides)
_static/            Site-specific static assets (style.css, images)
_routes/            Site-specific route handlers
_migrations/        Database migrations (SQLite by default)
src/                Site-specific PHP classes (ContactPage, Testimonial, etc.)
uploads/            User-uploaded media (managed by the CMS media library)
aspects/cms/        The CMS aspect — its own src/, _views/, _routes/, _static/, js/
```

## How aspects overlay

Mini scans paths in dependency order: the root app's files take priority, then aspects in reverse dependency order. If the root app defines `_views/partials/header.php`, it overrides the CMS aspect's version. This means the CMS ships sensible defaults that any site can replace file-by-file.

## Making pages

Routes are defined in `_content/routes.php` as an array mapping URL paths to Page objects:

```php
'/' => new Page('pages/home', title: 'Home'),
'/about' => new Page('pages/about', title: 'About Us'),
'/team/{slug}' => new Page('team/show', title: 'Team Member'),
```

Each Page points to a view template. Views are plain PHP files in `_views/` using Mini's template inheritance (`$this->extend('_layout.php')`, `$this->block('content')`).

## Editable content in views

Views use three helper functions to create inline-editable regions:

- `cms_text('file.json', 'Group|Label', position, 'default', 'h2')` — plain text, optionally wrapped in a tag
- `cms_html('file.json', 'Group|Label', position, '<p>default</p>')` — rich HTML content
- `cms_image('file.json', 'Group|Label', position, '/default.jpg', 'img', 'alt text', '16x9')` — responsive image with optional aspect ratio cropping

The first argument is a JSON file path relative to `_content/`. Content values are stored there. The "Group|Label" string determines how fields are grouped in the admin sidebar. Position is an integer that controls field ordering.

When the admin is logged in, these render with `data-cms-*` attributes so the admin panel can discover and edit them.

## Database and models

The demo site uses SQLite. Models extend `mini\Database\Model` and use attributes like `#[Table]`, `#[PrimaryKey]`, `#[Required]`, `#[MaxLength]`, `#[Title]`, `#[CreatedAt]`, `#[UpdatedAt]`.

To expose a model in the admin panel's DATA section, register it in `_content/models.php`:

```php
return [
    'testimonials' => (new Entity(Testimonial::class, icon: 'bi-chat-quote', pluralTitle: 'Testimonials'))
        ->withDefaultOrder('sort_order ASC'),
];
```

The Entity class wraps a Model with CMS UI configuration. The auto CRUD controller provides index/create/edit/show/delete views using generic templates by default. To customize the UI for an entity, override the view methods:

```php
'testimonials' => (new Entity(Testimonial::class, icon: 'bi-chat-quote'))
    ->withIndexView('admin/data/testimonials/index.php')
    ->withEditView('admin/data/testimonials/edit.php'),
```

Entity has `getIndexView()`, `getCreateView()`, `getEditView()`, and `getShowView()` — each returns a view path that defaults to the generic CRUD template. Override any of them with `withIndexView()` etc., or subclass Entity and override the getter methods directly. Then create the view file at that path in `_views/`.

## Entity relationships and the picker

When a model has a `#[ForeignKey]` attribute pointing to another model that is also registered as an Entity, the CMS automatically detects the relationship. In forms, the foreign key field renders as an entity picker instead of a plain number input. In detail views, the related entity is rendered inline with a link.

### Display and inline rendering

Each Entity has a **display column** — the column shown when the entity appears as a reference elsewhere (e.g. in a picker or a foreign key field). By default it's the first text column. Override with `->withDisplayColumn('company_name')`.

For richer rendering (e.g. a thumbnail + name), set a custom **inline view** with `->withInlineView('admin/data/customers/inline.php')`. The view receives `$item` and `$entity`. If no inline view is set, the display column value is used as plain escaped text.

### Entity picker (JS)

From JavaScript: `let result = await CMS.data.pick('customers')` opens a searchable, paginated picker dialog and returns `{id, display}` or `null` if cancelled. The picker reuses the data API (`/admin/api/data/{slug}/`).

To load an entity's inline HTML into a DOM element: `CMS.data.loadInline('customers', 5, targetEl)`.

### Data API

The data API is a controller (`DataApiController`) mounted at `/admin/api/data/{slug}/`:

- `GET /` — paginated list with `page`, `perPage`, `search`, `sort`, `dir` params. Returns `{rows, total, page, perPage, pages}`. Each row includes `_id` and `_display`.
- `GET /{id}` — single entity lookup. Returns `{id, display}`.
- `GET /{id}/inline` — server-rendered HTML fragment of the entity's inline view.

### Example: setting up related entities

```php
// _content/models.php
return [
    'customers' => (new Entity(Customer::class, icon: 'bi-people', pluralTitle: 'Customers'))
        ->withDisplayColumn('name'),
    'orders' => (new Entity(Order::class, icon: 'bi-receipt', pluralTitle: 'Orders'))
        ->withDefaultOrder('created_at DESC'),
];
```

The Order model declares the relationship via Mini's `#[ForeignKey]` attribute:

```php
#[ForeignKey(navigation: 'customer')]
public int $customer_id;

public Customer $customer;
```

The CMS detects this and renders `customer_id` as a picker field automatically.

## Views and queries

All views have a `query()` helper (defined in `_viewstart.php`) that returns a `mini\Database\Query`. It's immutable, so views can narrow results but never widen them:

```php
<?php foreach (query("SELECT * FROM testimonials WHERE featured = 1 ORDER BY sort_order") as $t): ?>
    <blockquote><?= \mini\h($t->quote) ?></blockquote>
<?php endforeach; ?>
```

Use `\mini\h()` for escaping, `\mini\Fmt::dateShort()` for dates, `\mini\render()` to include partials.

## Static assets and JS

Site styles go in `_static/style.css`. The CMS aspect's JS is built with esbuild:

```sh
cd aspects/cms && npm run build
```

Source modules are in `aspects/cms/js/`, the bundle outputs to `aspects/cms/_static/admin/dist/cms.min.js`. All vendor libraries (Bootstrap, AdminLTE, simple-datatables, Cropper.js) are served locally from `_static/admin/vendor/` — never link external CDNs. All CMS URLs (routes and static assets) live under the `/admin/` prefix to minimize the surface where application files can shadow CMS files through the overlay.

## AI assistant

The CMS includes an AI assistant accessible as a drawer in the page editor and as a standalone page at `/admin/ai/`. It uses an adapter pattern so different AI backends can be swapped in.

### Architecture

`AgentInterface` (`aspects/cms/src/Ai/AgentInterface.php`) defines the contract: `stream()`, `getHistory()`, `newConversation()`, `hasSession()`. The default implementation is `ClaudeCodeAgent`, which invokes the Claude CLI and reads conversation history from `~/.claude/` session files.

The agent is registered in the DI container via `bootstrap.php`. To swap in a different backend, register a different `AgentInterface` implementation.

### Session persistence

The agent manages a single long-lived conversation per site. `ClaudeCodeAgent` stores the session ID, PID, and last-known page in `.cms/ai-state.json` at the project root. Conversation history is read from Claude's own session JSONL files in `~/.claude/` — the CMS doesn't maintain a separate copy.

### Context injection

On the first message of a new session, the prompt endpoint prepends a `[CMS Context]` block containing the site name, available routes, data models, and the page the user is currently editing. When the user navigates to a different page mid-conversation, a shorter page-change note is injected. Subsequent messages on the same page are sent without context overhead.

### Resilient streaming

The Claude CLI process runs in the background, writing its stream-json output to `.cms/ai-stream.ndjson`. The HTTP stream endpoint tails this file using byte offsets for O(1) reconnection. Each streamed NDJSON line is wrapped as `{"pos": <byteOffset>, "msg": <original>}` — the client stores `pos` and sends it back as `?pos=N` on reconnect, so the server can `fseek` directly without re-reading. The Claude process keeps running regardless of HTTP connection state.

### History

History is loaded from Claude's session JSONL files and returns structured content blocks (text and tool_use) so the UI can render tool invocations alongside text. Results are capped at the last 50 messages for performance over long-lived conversations.

### API

- `POST /admin/api/ai/prompt` — submit a prompt. Launches Claude in the background. Accepts `{prompt, page}`.
- `GET /admin/api/ai/stream` — tail the current response. Supports `?pos=N` (byte offset) for reconnection. Returns NDJSON with `{pos, msg}` envelope.
- `GET /admin/api/ai/history` — load past conversation messages (last 50) with structured content blocks.
- `POST /admin/api/ai/reset` — start a new conversation.

## Constraints

- **No external CDNs.** Every asset must be served from the local filesystem. This is a privacy requirement.
- **Don't work around Mini bugs.** If something in the framework doesn't work as expected, discuss it rather than patching around it.
- **Use Mini's built-in tools.** Before reaching for a third-party package, check whether Mini already provides the functionality. It usually does — validation, mail, caching, auth, database, HTTP client are all built in.
- **Respect the overlay.** Put CMS defaults in `aspects/cms/`, site-specific overrides in the root app. Don't mix the two.
