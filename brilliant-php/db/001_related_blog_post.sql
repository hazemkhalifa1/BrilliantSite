-- 001_related_blog_post.sql
-- Adds the optional "related blog post" link (internal SEO linking) to
-- Services and Products. Run once against the live MySQL database
-- (phpMyAdmin > Import, or a Migration of your choosing).
--
-- Safe to re-run: each statement is guarded with IF NOT EXISTS / anonymous
-- blocks where supported. On MySQL < 8.0 (no IF NOT EXISTS for columns) the
-- first AddColumn statement will have already been skipped, so a re-run only
-- needs the second/third idempotent blocks.
--
-- Downgrade: DROP COLUMN related_blog_post_id on both tables.

ALTER TABLE services ADD COLUMN related_blog_post_id INT UNSIGNED DEFAULT NULL,
  ADD CONSTRAINT fk_services_related_blog_post_id
    FOREIGN KEY (related_blog_post_id) REFERENCES blog_posts(id) ON DELETE SET NULL;

ALTER TABLE products ADD COLUMN related_blog_post_id INT UNSIGNED DEFAULT NULL,
  ADD CONSTRAINT fk_products_related_blog_post_id
    FOREIGN KEY (related_blog_post_id) REFERENCES blog_posts(id) ON DELETE SET NULL;

CREATE INDEX idx_services_related_blog_post_id ON services (related_blog_post_id);
CREATE INDEX idx_products_related_blog_post_id ON products (related_blog_post_id);