
# LBH — Lightweight Blog Helpers for PHP

> **loadBlogHelpers (LBH)** is a modular collection of PHP utilities designed to simplify blog and content-driven development.  
> It provides clean helpers for SEO, Slugging, Text Extraction, HTML parsing, and network utilities — ready to drop into any PHP project.





## 🚀 Real-World Use Case

Imagine you’re running any PHP-based blog — whether it’s a small CMS you built yourself, a Next.js + PHP hybrid backend, or something like WordPress stripped down.

You’ll repeatedly need things like:

- Cutting long paragraphs into **short excerpts** for previews.
    
- Showing **estimated reading time** on articles.
    
- Generating **SEO meta tags** and **OpenGraph** data for social sharing.
    
- Making clean, readable **URLs and slugs**.
    
- Paginating article lists.
    
- Building a **Table of Contents** from article headings.
    
- Generating **RSS feeds** or **Sitemap XML**.
    

Rather than coding those from scratch every time, you build this once → then just `composer require ridenzy/lbh` in any future project.

It becomes your **personal developer toolkit** for all content-based sites.




---

## 🚀 Features

- **SEO Helpers** → Easily generate meta tags, sitemaps, canonical URLs, and analyze page text.
- **Content Helpers** → Slugify titles, extract keywords, parse HTML, and format text.
- **Page Helpers** → Make lightweight HTTP requests, strip markup, and extract visible DOM text.
- **PSR-4 Autoloading** → Installable and extendable as a modern Composer package.
- **Framework-Agnostic** → Works in plain PHP, Laravel, Symfony, WordPress, or custom stacks.

---

## 📦 Installation

From [Packagist](https://packagist.org/packages/ridenzy/lbh):

```bash
composer require ridenzy/lbh:^1.0
```

---

***Or directly from GitHub:**

```bash
composer config repositories.lbh '{"type":"vcs","url":"https://github.com/ridenzy/lbh"}'
composer require ridenzy/lbh:dev-main
```

---








Possible to add features,  More to come:

--> 🔍 **2. SEO, Metadata & Social Sharing** 

| `keywords_from_content(text)` | Auto-generates keyword list by frequency analysis.    | SEO auto-tagging or content suggestions.  | ✅
| ----------------------------- | ----------------------------------------------------- | ----------------------------------------- |


--> 📚 **3. Post Management & Relationships**

| `filter_by_tag(posts, tag)`              | Filters posts array by a specific tag.           | Tag-based archive pages.               |
| ---------------------------------------- | ------------------------------------------------ | -------------------------------------- |
| `group_by_month(posts)`                  | Groups posts by publish month/year.              | Archives section.                      |


--> 🗺️ **4. Feeds, Discovery & Automation**
| `ping_search_engines(sitemap_url)`    | Sends sitemap update to Google/Bing.              | Triggered on new post publish.  |
| ------------------------------------- | ------------------------------------------------- | ------------------------------- |
| `feed_to_json(rss_url)`               | Parses RSS XML into JSON.                         | Feed readers, integrations.     |
| `notify_subscribers(post)`            | Sends email or webhook notification for new post. | Newsletter, push notifications. |
| `schedule_publish(post_id, datetime)` | Delays publication until a given time.            | Scheduled posts.                |


--> 🖼️ **5. Image & Media Handling**
| `generate_thumbnail(image_path, width, height)`  | Creates smaller preview images.                         | Blog home/featured posts.        |
| ------------------------------------------------ | ------------------------------------------------------- | -------------------------------- |
| `lazy_load_image_tag(src, alt)`                  | Returns `<img loading="lazy">` tag.                     | Improves performance.            |
| `optimize_image(image_path)`                     | Compresses images before upload.                        | Faster page loading.             |
| `embed_video(url)`                               | Converts YouTube/Vimeo link into responsive embed code. | Inline media embedding.          |


--> 🔐 **6. Authentication & Admin Helpers**
| `login(email, password)`          | Authenticates a user.                      | Blog admin dashboard.    |
| --------------------------------- | ------------------------------------------ | ------------------------ |
| `logout()`                        | Clears session.                            | User account management. |
| `check_auth()`                    | Ensures a session is valid.                | Protecting admin pages.  |
| `hash_password(password)`         | Uses `password_hash()` for secure storage. | Registration and login.  |
| `verify_password(password, hash)` | Validates a given password.                | Login check.             |


--> 🧰 **7. Database & Storage**
| `connect_db(config)`         | Creates PDO/MySQL connection.             | Core data access layer.   |
| ---------------------------- | ----------------------------------------- | ------------------------- |
| `fetch_posts(limit, offset)` | Retrieves posts from DB.                  | Blog index or API.        |
| `save_post(data)`            | Inserts or updates post data.             | Admin editor save action. |
| `delete_post(id)`            | Deletes post safely.                      | Admin deletion.           |
| `backup_database()`          | Creates SQL backup of posts and metadata. | Site maintenance.         |


--> 📤 **8. API & Integration Functions**
| `api_get(endpoint, params)`       | Performs GET request to remote API.  | Pulls comments, stats, or external data. |
| --------------------------------- | ------------------------------------ | ---------------------------------------- |
| `api_post(endpoint, data)`        | Posts to remote API (e.g., webhook). | Newsletter automation.                   |
| `webhook_receive(payload)`        | Handles inbound webhook.             | Auto-publishing, integrations.           |
| `json_response(data, status=200)` | Outputs clean JSON for frontend.     | REST API endpoints.                      |
| `markdown_to_html(markdown)`      | Converts markdown to HTML.           | Editor or import/export feature.         |


--> 📈 **9. Analytics, Stats & Optimization**
| `count_views(post_id)`       | Increments a post’s view count.       | Popular posts widget.     |
| ---------------------------- | ------------------------------------- | ------------------------- |
| `most_viewed(posts, limit)`  | Returns top posts by views.           | Sidebar popularity list.  |
| `record_referrer()`          | Logs where a visitor came from.       | Analytics and insights.   |
| `ab_test_render(variants)`   | Randomly selects between A/B content. | Experimentation.          |
| `cache_page(key, html, ttl)` | Saves rendered HTML to cache.         | Performance optimization. |


--> 🧩 **10. Frontend Presentation Helpers**
| `render_post_card(post)`        | Returns an HTML snippet for a post preview.       | Home and category pages.     |
| ------------------------------- | ------------------------------------------------- | ---------------------------- |
| `breadcrumb(path_array)`        | Builds breadcrumb navigation.                     | SEO + UX improvement.        |
| `format_date(date, locale)`     | Displays date in friendly format.                 | “Published on Oct 26, 2025”. |
| `highlight_search(query, text)` | Highlights search term in preview.                | On search results page.      |
| `truncate_html(html, limit)`    | Safely shortens HTML while keeping tags balanced. | Preview snippets.            |


## 🧭 **11. Developer & Maintenance Functions**
| `clear_cache()`      | Clears cached HTML/pages.            | Admin maintenance button.        |
| -------------------- | ------------------------------------ | -------------------------------- |
| `rebuild_index()`    | Regenerates search index or sitemap. | After mass updates.              |
| `export_json(posts)` | Exports all posts to JSON.           | Backup or static site generator. |
| `import_json(file)`  | Imports posts from JSON file.        | Migration from another CMS.      |
| `env(key, default)`  | Reads environment variables.         | Config management.               |


## 🧱 **12. Special Use-Case Helpers**
| `generate_excerpt_image_overlay(title, cover)` | Creates a text overlay image (title on cover). | Social share image automation. |
| ---------------------------------------------- | ---------------------------------------------- | ------------------------------ |
| `create_post_slug_from_id(id, title)`          | Makes unique slugs combining ID + title.       | Avoids duplicate URLs.         |
| `auto_save_draft(content)`                     | Autosaves during editing.                      | Editor session recovery.       |
| `generate_share_links(url, title)`             | Builds share URLs for Twitter, LinkedIn, etc.  | “Share this post” buttons.     |
| `shortcode_parser(content)`                    | Replaces `[gallery]` or `[video]` with HTML.   | Extensible post content.       |




----


# At the end of this project:


Extend the Blog Helpers project into a proper “Publishing Module” — one that can be run as a CLI command (php blog-helpers publish) to automatically regenerate RSS + Sitemap + image metadata for sites.
That would make it a production-grade tool.