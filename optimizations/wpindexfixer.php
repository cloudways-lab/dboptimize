<?php
class WP_DbOptimization_wpindexfixer extends WP_DbOptimization {

    public function optimize() {
		global $wpdb;
        $table_prefix = $wpdb->prefix;
        
        //users
        $this->query('DELETE FROM '.$table_prefix.'users WHERE ID = 0');
        $this->query('ALTER TABLE '.$table_prefix.'users ADD PRIMARY KEY  (ID)');
        $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_login_key (user_login)');
        $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_nicename (user_nicename)');
        $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_email (user_email)');
        $this->query('ALTER TABLE '.$table_prefix.'users MODIFY ID bigint(20) unsigned NOT NULL auto_increment');

        //usermeta
        $this->query('DELETE FROM '.$table_prefix.'usermeta WHERE umeta_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD PRIMARY KEY  (umeta_id)');
        $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD KEY user_id (user_id)');
        $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD KEY meta_key (meta_key(191))');
        $this->query('ALTER TABLE '.$table_prefix.'usermeta MODIFY umeta_id bigint(20) unsigned NOT NULL auto_increment');

        // posts
        $this->query('DELETE FROM '.$table_prefix.'posts WHERE ID = 0');
        $this->query(('ALTER TABLE '.$table_prefix.'posts ADD PRIMARY KEY  (ID)'));
        $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_name (post_name(191))');
        $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY type_status_date (post_type,post_status,post_date,ID)');
        $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_parent (post_parent)');
        $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_author (post_author)');
        $this->query('ALTER TABLE '.$table_prefix.'posts MODIFY ID bigint(20) unsigned NOT NULL auto_increment');

        //comments
        $this->query('DELETE FROM '.$table_prefix.'comments WHERE comment_ID = 0');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD PRIMARY KEY  (comment_ID)');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_post_ID (comment_post_ID)');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_approved_date_gmt (comment_approved,comment_date_gmt)');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_date_gmt (comment_date_gmt)');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_parent (comment_parent)');
        $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_author_email (comment_author_email(10))');
        $this->query('ALTER TABLE '.$table_prefix.'comments MODIFY comment_ID bigint(20) unsigned NOT NULL auto_increment');

        //links
        $this->query('DELETE FROM '.$table_prefix.'links WHERE link_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'links ADD PRIMARY KEY  (link_id)');
        $this->query('ALTER TABLE '.$table_prefix.'links ADD KEY link_visible (link_visible)');
        $this->query('ALTER TABLE '.$table_prefix.'links MODIFY link_id bigint(20) unsigned NOT NULL auto_increment');

        //options
        $this->query('DELETE FROM '.$table_prefix.'options WHERE option_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'options ADD PRIMARY KEY  (option_id)');
        $this->query('ALTER TABLE '.$table_prefix.'options ADD UNIQUE KEY option_name (option_name)');
        $this->query('ALTER TABLE '.$table_prefix.'options ADD KEY autoload (autoload)');
        $this->query('ALTER TABLE '.$table_prefix.'options MODIFY option_id bigint(20) unsigned NOT NULL auto_increment');

        //postmeta
        $this->query('DELETE FROM '.$table_prefix.'postmeta WHERE meta_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD PRIMARY KEY  (meta_id)');
        $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD KEY post_id (post_id)');
        $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD KEY meta_key (meta_key(191))');
        $this->query('ALTER TABLE '.$table_prefix.'postmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');

        //terms
        $this->query('DELETE FROM '.$table_prefix.'terms WHERE term_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'terms ADD PRIMARY KEY  (term_id)');
        $this->query('ALTER TABLE '.$table_prefix.'terms ADD KEY slug (slug(191))');
        $this->query('ALTER TABLE '.$table_prefix.'terms ADD KEY name (name(191))');
        $this->query('ALTER TABLE '.$table_prefix.'terms MODIFY term_id bigint(20) unsigned NOT NULL auto_increment');

        //term taxonomy
        $this->query('DELETE FROM '.$table_prefix.'term_taxonomy WHERE term_taxonomy_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD PRIMARY KEY  (term_taxonomy_id)');
        $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD UNIQUE KEY term_id_taxonomy (term_id,taxonomy)');
        $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD KEY taxonomy (taxonomy)');
        $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy MODIFY term_taxonomy_id bigint(20) unsigned NOT NULL auto_increment');


        //term relationships
        $this->query('DELETE FROM '.$table_prefix.'term_relationships WHERE object_id = 0');
        $this->query('DELETE FROM '.$table_prefix.'term_relationships WHERE term_taxonomy_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'term_relationships ADD PRIMARY KEY  (object_id,term_taxonomy_id)');
        $this->query('ALTER TABLE '.$table_prefix.'term_relationships ADD KEY term_taxonomy_id (term_taxonomy_id)');

        //termmeta
        $this->query('DELETE FROM '.$table_prefix.'termmeta WHERE meta_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD PRIMARY KEY  (meta_id)');
        $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD KEY term_id (term_id)');
        $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD KEY meta_key (meta_key(191))');
        $this->query('ALTER TABLE '.$table_prefix.'termmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');

        // comment meta
        $this->query('DELETE FROM '.$table_prefix.'commentmeta WHERE meta_id = 0');
        $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD PRIMARY KEY  (meta_id)');
        $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD KEY comment_id (comment_id)');
        $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD KEY meta_key (meta_key(191))');
        $this->query('ALTER TABLE '.$table_prefix.'commentmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');

	}

	public function after_get_info() {
        global $wpdb;
		if (isset($this->table_found['users']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['user_meta']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['posts']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['comments']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['links']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['options']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['postmeta']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['terms']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['term_taxonomy']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['term_relationships']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['termmeta']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}
        if (isset($this->table_found['commentmeta']) && $this->table_found['users'] == false) {
			$message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
		}

        $this->logger->info($message);
		$this->register_output($message);
	}

	public function get_info() {
		global $wpdb;

		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "users" . "'") == $wpdb->prefix . "users") {
			$this->table_found['users'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "user_meta" . "'") == $wpdb->prefix . "user_meta") {
			$this->table_found['user_meta'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "posts" . "'") == $wpdb->prefix . "posts") {
			$this->table_found['posts'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "comments" . "'") == $wpdb->prefix . "comments") {
			$this->table_found['comments'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "links" . "'") == $wpdb->prefix . "links") {
			$this->table_found['links'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "options" . "'") == $wpdb->prefix . "options") {
			$this->table_found['options'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "postmeta" . "'") == $wpdb->prefix . "postmeta") {
			$this->table_found['postmeta'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "terms" . "'") == $wpdb->prefix . "terms") {
			$this->table_found['terms'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "term_taxonomy" . "'") == $wpdb->prefix . "term_taxonomy") {
			$this->table_found['term_taxonomy'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "term_relationships" . "'") == $wpdb->prefix . "term_relationships") {
			$this->table_found['term_relationships'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "termmeta" . "'") == $wpdb->prefix . "termmeta") {
			$this->table_found['termmeta'] = false;
		}
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "commentmeta" . "'") == $wpdb->prefix . "commentmeta") {
			$this->table_found['commentmeta'] = false;
		}
	}

	public function settings_label() {
		return __('WP Index Fixer', 'wp-dboptimize');
	}

	public function after_optimize() {
		$message = sprintf('WP Indexer fixed');
		$this->logger->info($message);
		$this->register_output($message);

	}

}    
