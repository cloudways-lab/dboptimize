<?php
class WP_DbOptimization_wpindexfixer extends WP_DbOptimization {

    public function optimize() {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        
        //users
        $this->query('DELETE FROM '.$table_prefix.'users WHERE ID = 0');
        $u_pri = $this->query("select * from information_schema.columns where table_name='".$table_prefix."users' and column_key='PRI'");
        if (!$u_pri){
            $this->query('ALTER TABLE '.$table_prefix.'users ADD PRIMARY KEY IF NOT EXISTS (ID)');
        }
        $u_l_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_login'");
        if (!$u_l_key) {
            $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_login_key (user_login)');
        }
        $u_nice = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_nicename'");
        if (!$u_nice) {
            $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_nicename (user_nicename)');
        }
        $u_email = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_email'");
        if (!$u_email) {
            $this->query('ALTER TABLE '.$table_prefix.'users ADD KEY user_email (user_email)');
        }
        $u_users = $this->query("select * from information_schema.columns where table_name='".$table_prefix."users' and column_name='ID' AND EXTRA like '%auto_increment%'");
        if (!$u_users) {
            $this->query('ALTER TABLE '.$table_prefix.'users MODIFY ID bigint(20) unsigned NOT NULL auto_increment');
        }


        // //usermeta

        $this->query('DELETE FROM '.$table_prefix.'usermeta WHERE umeta_id = 0');
        $umeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."usermeta' and column_key='PRI'");
        if (!$umeta_primary){
            $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD PRIMARY KEY  (umeta_id)');
        }    
        $u_meta_userid_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."usermeta' and column_name='user_id'");
        if (!$u_meta_userid_key) {
            $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD KEY user_id (user_id)');
        }    
        $u_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."usermeta' and column_name='meta_key'");
        if (!$u_meta_key) {
            $this->query('ALTER TABLE '.$table_prefix.'usermeta ADD KEY meta_key (meta_key(191))');
        }    
        $u_meta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."usermeta' and column_name='umeta_id' AND EXTRA like '%auto_increment%'");
        if (!$u_meta_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'usermeta MODIFY umeta_id bigint(20) unsigned NOT NULL auto_increment');
        }    

        // // posts
        $this->query('DELETE FROM '.$table_prefix.'posts WHERE ID = 0');
        $post_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."posts' and column_key='PRI'");
        if (!$post_primary) {
            $this->query(('ALTER TABLE '.$table_prefix.'posts ADD PRIMARY KEY  (ID)'));
        }
        
        $post_postname_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_name'");
        if (!$post_postname_key) {
            $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_name (post_name(191))');
        }
        
        $post_type_status_date_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name in ('post_type','post_status','post_date','ID')");
        if (!$post_type_status_date_key) {
            $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY type_status_date (post_type,post_status,post_date,ID)');
        }    
        $post_post_parent = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_parent'");
        if (!$post_post_parent) {
            $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_parent (post_parent)');
        }    
        $post_post_author_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_author'");
        if (!$post_post_author_key) {
            $this->query('ALTER TABLE '.$table_prefix.'posts ADD KEY post_author (post_author)');
        }    
        $post_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."posts' and column_name='ID' AND EXTRA like '%auto_increment%'");
        if (!$post_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'posts MODIFY ID bigint(20) unsigned NOT NULL auto_increment');
        }    

        // //comments

        $this->query('DELETE FROM '.$table_prefix.'comments WHERE comment_ID = 0');
        $comment_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."comments' and column_key='PRI'");
        if (!$comment_primary){
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD PRIMARY KEY  (comment_ID)');
        }    
        $comment_post_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_post_ID'");
        if (!$comment_post_id_key) {
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_post_ID (comment_post_ID)');
        }    
        $comment_approved_date_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name in ('comment_approved', 'comment_date_gmt')");
        if (!$comment_approved_date_key) {
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_approved_date_gmt (comment_approved,comment_date_gmt)');
        }    
        $comment_date_gmt_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_date_gmt'");
        if (!$comment_date_gmt_key) {
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_date_gmt (comment_date_gmt)');
        }
        $comment_parent_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_parent'");
        if (!$comment_parent_key) {
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_parent (comment_parent)');
        }    
        $comment_author_email_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_author_email'");
        if (!$comment_author_email_key) {
            $this->query('ALTER TABLE '.$table_prefix.'comments ADD KEY comment_author_email (comment_author_email(10))');
        }
        $comment_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."comments' and column_name='comment_ID' AND EXTRA like '%auto_increment%'");
        if (!$comment_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'comments MODIFY comment_ID bigint(20) unsigned NOT NULL auto_increment');
        }

        // //links
        $this->query('DELETE FROM '.$table_prefix.'links WHERE link_id = 0');
        $link_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."links' and column_key='PRI'");
        if (!$link_primary){
            $this->query('ALTER TABLE '.$table_prefix.'links ADD PRIMARY KEY  (link_id)');
        }
        $links_visible_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."links' and column_name='link_visible'");
        if (!$links_visible_key) {
            $this->query('ALTER TABLE '.$table_prefix.'links ADD KEY link_visible (link_visible)');
        }    
        $links_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."links' and column_name='link_id' AND EXTRA like '%auto_increment%'");
        if (!$links_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'links MODIFY link_id bigint(20) unsigned NOT NULL auto_increment');
        }

        // //options

        $this->query('DELETE FROM '.$table_prefix.'options WHERE option_id = 0');

        $options_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."options' and column_key='PRI'");
        if (!$options_primary){
            $this->query('ALTER TABLE '.$table_prefix.'options ADD PRIMARY KEY  (option_id)');
        }

        $options_option_name_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."options' and column_name='option_name'");
        if (!$options_option_name_key) {
            $this->query('ALTER TABLE '.$table_prefix.'options ADD UNIQUE KEY option_name (option_name)');
        }
        $options_autoload_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."options' and column_name='autoload'");
        if (!$options_autoload_key) {
            $this->query('ALTER TABLE '.$table_prefix.'options ADD KEY autoload (autoload)');
        }    

        $options_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."options' and column_name='option_id' AND EXTRA like '%auto_increment%'");
        if (!$options_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'options MODIFY option_id bigint(20) unsigned NOT NULL auto_increment');
        }    
        // //postmeta
        $this->query('DELETE FROM '.$table_prefix.'postmeta WHERE meta_id = 0');
        
        $postmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."postmeta' and column_key='PRI'");
        if (!$postmeta_primary){
            $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD PRIMARY KEY  (meta_id)');
        }    
        $postmeta_postid_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."postmeta' and column_name='post_id'");
        if (!$postmeta_postid_key) {
            $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD KEY post_id (post_id)');
        }
        $postmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."postmeta' and column_name='meta_key'");
        if (!$postmeta_meta_key) {
            $this->query('ALTER TABLE '.$table_prefix.'postmeta ADD KEY meta_key (meta_key(191))');
        }
        $postmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."postmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$postmeta_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'postmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');
        }

        // //terms
        $this->query('DELETE FROM '.$table_prefix.'terms WHERE term_id = 0');
        $terms_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."terms' and column_key='PRI'");
        if (!$terms_primary){
            $this->query('ALTER TABLE '.$table_prefix.'terms ADD PRIMARY KEY  (term_id)');
        }
        $term_slug_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."terms' and column_name='slug'");
        if (!$term_slug_key) {
            $this->query('ALTER TABLE '.$table_prefix.'terms ADD KEY slug (slug(191))');
        }
        $term_name_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."terms' and column_name='name'");
        if (!$term_name_key) {
            $this->query('ALTER TABLE '.$table_prefix.'terms ADD KEY name (name(191))');
        }
        $terms_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."terms' and column_name='term_id' AND EXTRA like '%auto_increment%'");
        if (!$terms_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'terms MODIFY term_id bigint(20) unsigned NOT NULL auto_increment');
        }

        // //term taxonomy
        $this->query('DELETE FROM '.$table_prefix.'term_taxonomy WHERE term_taxonomy_id = 0');
        $terms_taxonomy_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_taxonomy' and column_key='PRI'");
        if (!$terms_taxonomy_primary){
            $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD PRIMARY KEY  (term_taxonomy_id)');
        }
        $terms_taxonomy_term_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_taxonomy' and column_name in ('term_id','taxonomy')");
        if (!$terms_taxonomy_term_id_key) {
            $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD UNIQUE KEY term_id_taxonomy (term_id,taxonomy)');
        }

        $terms_taxonomy_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_taxonomy' and column_name='taxonomy'");
        if (!$terms_taxonomy_key) {
            $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy ADD KEY taxonomy (taxonomy)');
        }
        $term_taxonomy_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_taxonomy' and column_name='term_taxonomy_id' AND EXTRA like '%auto_increment%'");
        if (!$term_taxonomy_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'term_taxonomy MODIFY term_taxonomy_id bigint(20) unsigned NOT NULL auto_increment');
        }


        // //term relationships
        $this->query('DELETE FROM '.$table_prefix.'term_relationships WHERE object_id = 0');
        $term_relationship_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_relationships' and column_key='PRI'");
        if (!$term_relationship_primary){
            $this->query('ALTER TABLE '.$table_prefix.'term_relationships ADD PRIMARY KEY  (object_id,term_taxonomy_id)');
        }
        $terms_relationship_term_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_relationships' and column_name='term_taxonomy_id'");
        if (!$terms_relationship_term_key) {        
            $this->query('ALTER TABLE '.$table_prefix.'term_relationships ADD KEY term_taxonomy_id (term_taxonomy_id)');
        }    
        // //termmeta
        $this->query('DELETE FROM '.$table_prefix.'termmeta WHERE meta_id = 0');
        $termmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."termmeta' and column_key='PRI'");
        if (!$termmeta_primary){
            $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD PRIMARY KEY  (meta_id)');
        }
        $termmeta_term_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."termmeta' and column_name='term_id'");
        if (!$termmeta_term_id_key) {        
            $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD KEY term_id (term_id)');
        }
        $termmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."termmeta' and column_name='meta_key'");
        if (!$termmeta_meta_key) {        
            $this->query('ALTER TABLE '.$table_prefix.'termmeta ADD KEY meta_key (meta_key(191))');
        }
        $termmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."termmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$termmeta_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'termmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');
        }

        // // comment meta
        $this->query('DELETE FROM '.$table_prefix.'commentmeta WHERE meta_id = 0');
        $commentmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."commentmeta' and column_key='PRI'");
        if (!$commentmeta_primary){
            $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD PRIMARY KEY  (meta_id)');
        }  
        $commentmeta_comment_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."commentmeta' and column_name='comment_id'");
        if (!$commentmeta_comment_id_key) {        
            $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD KEY comment_id (comment_id)');
        }
        $commentmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."commentmeta' and column_name='meta_key'");
        if (!$commentmeta_meta_key) {        
            $this->query('ALTER TABLE '.$table_prefix.'commentmeta ADD KEY meta_key (meta_key(191))');
        }
        $commentmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."commentmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$commentmeta_auto) {
            $this->query('ALTER TABLE '.$table_prefix.'commentmeta MODIFY meta_id bigint(20) unsigned NOT NULL auto_increment');
        }  

    }

    public function after_get_info() {
        global $wpdb;
        if (isset($this->table_found['users']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'users tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['user_meta']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'user_meta tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['posts']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'posts tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['comments']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'comments tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['links']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['options']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'options tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['postmeta']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'postmeta tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['terms']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'terms tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['term_taxonomy']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'term_taxonomy tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['term_relationships']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'term_relationships tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['columns']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'termmeta tables found', 'wp-dboptimize');
        }
        if (isset($this->table_found['commentmeta']) && $this->table_found['users'] == false) {
            $message = sprintf($wpdb->prefix.'commentmeta tables found', 'wp-dboptimize');
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
        return __('WP Index Fixer. Use --dry for a dry run to check what will be indexed', 'wp-dboptimize');
    }

    public function after_optimize() {
        $message = sprintf('WP Indexer fixed');
        $this->logger->info($message);
        $this->register_output($message);

    }

    public function dry_run() {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $message = '';
        $total_indexes = 0;
        
        $u_pri = $this->query("select * from information_schema.columns where table_name='".$table_prefix."users' and column_key='PRI'");
        if (!$u_pri){
            $message.= "Add primary key on ".$table_prefix."users table\n";
            $total_indexes+=1;
        }
        $u_l_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_login'");
        if (!$u_l_key) {
            $message.= "Add index key on ".$table_prefix."users table user_login column\n";
            $total_indexes+=1;
        }
        $u_nice = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_nicename'");
        if (!$u_nice) {
            $message.= "Add index key on ".$table_prefix."users table user_nicename column\n";
            $total_indexes+=1;
        }
        $u_email = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."users' and column_name='user_email'");
        if (!$u_email) {
            $message.= "Add index key on ".$table_prefix."users table user_email column\n";
            $total_indexes+=1;
        }
        $u_users = $this->query("select * from information_schema.columns where table_name='".$table_prefix."users' and column_name='ID' AND EXTRA like '%auto_increment%'");
        if (!$u_users) {
            $message.= "Add autoincrement on ".$table_prefix."users table ID column\n";
            $total_indexes+=1;
        }


        // //usermeta

        $this->query('DELETE FROM '.$table_prefix.'usermeta WHERE umeta_id = 0');
        $umeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."usermeta' and column_key='PRI'");
        if (!$umeta_primary){
            $message.= "Add primary key on ".$table_prefix."usermeta table user_email\n";
            $total_indexes+=1;
        }    
        $u_meta_userid_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."usermeta' and column_name='user_id'");
        if (!$u_meta_userid_key) {
            $message.= "Add index key on ".$table_prefix."usermeta table column user_id\n";
            $total_indexes+=1;
        }    
        $u_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."usermeta' and column_name='meta_key'");
        if (!$u_meta_key) {
            $message.= "Add index key on ".$table_prefix."usermeta table column meta_key\n";
            $total_indexes+=1;
        }    
        $u_meta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."usermeta' and column_name='umeta_id' AND EXTRA like '%auto_increment%'");
        if (!$u_meta_auto) {
            $message.= "Add index key on ".$table_prefix."usermeta table column umeta_id\n";
            $total_indexes+=1;
        }    

        // // posts
        $this->query('DELETE FROM '.$table_prefix.'posts WHERE ID = 0');
        $post_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."posts' and column_key='PRI'");
        if (!$post_primary) {
            $message.= "Add primary key on ".$table_prefix."columns table\n";
            $total_indexes+=1;
        }
        
        $post_postname_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_name'");
        if (!$post_postname_key) {
            $message.= "Add index key on ".$table_prefix."columns table column post_name\n";
            $total_indexes+=1;
        }
        
        $post_type_status_date_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name in ('post_type','post_status','post_date','ID')");
        if (!$post_type_status_date_key) {
            $message.= "Add index key on ".$table_prefix."columns table column post_type, post_status, post_date, ID\n";
            $total_indexes+=1;
        }    
        $post_post_parent = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_parent'");
        if (!$post_post_parent) {
            $message.= "Add index key on ".$table_prefix."columns table column post_parent\n";
            $total_indexes+=1;
        }    
        $post_post_author_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."posts' and column_name='post_author'");
        if (!$post_post_author_key) {
            $message.= "Add index key on ".$table_prefix."columns table column post_author\n";
            $total_indexes+=1;
        }    
        $post_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."posts' and column_name='ID' AND EXTRA like '%auto_increment%'");
        if (!$post_auto) {
            $message.= "Add autoincrement on ".$table_prefix."columns table column ID\n";
            $total_indexes+=1;
        }    

        // //comments

        $this->query('DELETE FROM '.$table_prefix.'comments WHERE comment_ID = 0');
        $comment_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."comments' and column_key='PRI'");
        if (!$comment_primary){
            $message.= "Add primary key on ".$table_prefix."comments table\n";
            $total_indexes+=1;
        }    
        $comment_post_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_post_ID'");
        if (!$comment_post_id_key) {
            $message.= "Add index key on ".$table_prefix."comments table column comment_post_ID\n";
            $total_indexes+=1;
        }    
        $comment_approved_date_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name in ('comment_approved', 'comment_date_gmt')");
        if (!$comment_approved_date_key) {
            $message.= "Add index key on ".$table_prefix."comments table column comment_approved, comment_date_gmt\n";
            $total_indexes+=1;
        }    
        $comment_date_gmt_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_date_gmt'");
        if (!$comment_date_gmt_key) {
            $message.= "Add index key on ".$table_prefix."comments table column comment_date_gmt\n";
            $total_indexes+=1;
        }
        $comment_parent_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_parent'");
        if (!$comment_parent_key) {
            $message.= "Add index key on ".$table_prefix."comments table column comment_parent\n";
            $total_indexes+=1;
        }    
        $comment_author_email_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."comments' and column_name='comment_author_email'");
        if (!$comment_author_email_key) {
            $message.= "Add index key on ".$table_prefix."comments table column comment_author_email\n";
            $total_indexes+=1;
        }
        $comment_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."comments' and column_name='comment_ID' AND EXTRA like '%auto_increment%'");
        if (!$comment_auto) {
            $message.= "Add autoincrement on ".$table_prefix."comments table column comment_ID\n";
            $total_indexes+=1;
        }

        // //links
        $this->query('DELETE FROM '.$table_prefix.'links WHERE link_id = 0');
        $link_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."links' and column_key='PRI'");
        if (!$link_primary){
            $message.= "Add primary key on ".$table_prefix."links table\n";
            $total_indexes+=1;
        }
        $links_visible_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."links' and column_name='link_visible'");
        if (!$links_visible_key) {
            $message.= "Add index key on ".$table_prefix."links table column link_visible\n";
            $total_indexes+=1;
        }    
        $links_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."links' and column_name='link_id' AND EXTRA like '%auto_increment%'");
        if (!$links_auto) {
            $message.= "Add autoincrement on ".$table_prefix."links table column link_id\n";
            $total_indexes+=1;
        }

        // //options

        $this->query('DELETE FROM '.$table_prefix.'options WHERE option_id = 0');

        $options_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."options' and column_key='PRI'");
        if (!$options_primary){
            $message.= "Add primary key on ".$table_prefix."options table\n";
            $total_indexes+=1;
        }

        $options_option_name_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."options' and column_name='option_name'");
        if (!$options_option_name_key) {
            $message.= "Add index key on ".$table_prefix."options table column option_name\n";
            $total_indexes+=1;
        }
        $options_autoload_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."options' and column_name='autoload'");
        if (!$options_autoload_key) {
            $message.= "Add index key on ".$table_prefix."options table column autoload\n";
            $total_indexes+=1;
        }    

        $options_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."options' and column_name='option_id' AND EXTRA like '%auto_increment%'");
        if (!$options_auto) {
            $message.= "Add autoincrement on ".$table_prefix."options table column option_id\n";
            $total_indexes+=1;
        }    
        // //postmeta
        $this->query('DELETE FROM '.$table_prefix.'postmeta WHERE meta_id = 0');
        
        $postmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."postmeta' and column_key='PRI'");
        if (!$postmeta_primary){
            $message.= "Add primary key on ".$table_prefix."postmeta table\n";
            $total_indexes+=1;
        }    
        $postmeta_postid_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."postmeta' and column_name='post_id'");
        if (!$postmeta_postid_key) {
            $message.= "Add index key on ".$table_prefix."postmeta table column post_id\n";
            $total_indexes+=1;
        }
        $postmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."postmeta' and column_name='meta_key'");
        if (!$postmeta_meta_key) {
            $message.= "Add index key on ".$table_prefix."postmeta table column meta_key\n";
            $total_indexes+=1;
        }
        $postmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."postmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$postmeta_auto) {
            $message.= "Add autoincrement on ".$table_prefix."postmeta table column meta_id\n";
            $total_indexes+=1;
        }

        // //terms
        $this->query('DELETE FROM '.$table_prefix.'terms WHERE term_id = 0');
        $terms_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."terms' and column_key='PRI'");
        if (!$terms_primary){
            $message.= "Add primary key on ".$table_prefix."terms table\n";
            $total_indexes+=1;
        }
        $term_slug_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."terms' and column_name='slug'");
        if (!$term_slug_key) {
            $message.= "Add index key on ".$table_prefix."terms table column slug\n";
            $total_indexes+=1;
        }
        $term_name_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."terms' and column_name='name'");
        if (!$term_name_key) {
            $message.= "Add index key on ".$table_prefix."terms table column name\n";
            $total_indexes+=1;
        }
        $terms_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."terms' and column_name='term_id' AND EXTRA like '%auto_increment%'");
        if (!$terms_auto) {
            $message.= "Add autoincrement on ".$table_prefix."terms table column term_id\n";
            $total_indexes+=1;
        }

        // //term taxonomy
        $this->query('DELETE FROM '.$table_prefix.'term_taxonomy WHERE term_taxonomy_id = 0');
        $terms_taxonomy_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_taxonomy' and column_key='PRI'");
        if (!$terms_taxonomy_primary){
            $message.= "Add primary key on ".$table_prefix."term_taxonomy table\n";
            $total_indexes+=1;
        }
        $terms_taxonomy_term_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_taxonomy' and column_name in ('term_id','taxonomy')");
        if (!$terms_taxonomy_term_id_key) {
            $message.= "Add index key on ".$table_prefix."term_taxonomy table column term_id, taxonomy\n";
            $total_indexes+=1;
        }

        $terms_taxonomy_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_taxonomy' and column_name='taxonomy'");
        if (!$terms_taxonomy_key) {
            $message.= "Add index key on ".$table_prefix."term_taxonomy table column taxonomy\n";
            $total_indexes+=1;

        }
        $term_taxonomy_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_taxonomy' and column_name='term_taxonomy_id' AND EXTRA like '%auto_increment%'");
        if (!$term_taxonomy_auto) {
            $message.= "Add autoincrement on ".$table_prefix."term_taxonomy table column term_taxonomy_id\n";
            $total_indexes+=1;
        }


        // //term relationships
        $this->query('DELETE FROM '.$table_prefix.'term_relationships WHERE object_id = 0');
        $term_relationship_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."term_relationships' and column_key='PRI'");
        if (!$term_relationship_primary){
            $message.= "Add primary key on ".$table_prefix."term_relationships table\n";
            $total_indexes+=1;
        }
        $terms_relationship_term_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."term_relationships' and column_name='term_taxonomy_id'");
        if (!$terms_relationship_term_key) {        
            $message.= "Add index key on ".$table_prefix."term_relationships table column term_taxonomy_id\n";
            $total_indexes+=1;
        }    
        // //termmeta
        $this->query('DELETE FROM '.$table_prefix.'termmeta WHERE meta_id = 0');
        $termmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."termmeta' and column_key='PRI'");
        if (!$termmeta_primary){
            $message.= "Add pirmary key on ".$table_prefix."termmeta table\n";
            $total_indexes+=1;
        }
        $termmeta_term_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."termmeta' and column_name='term_id'");
        if (!$termmeta_term_id_key) {        
            $message.= "Add index key on ".$table_prefix."termmeta table column term_id\n";
            $total_indexes+=1;
        }
        $termmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."termmeta' and column_name='meta_key'");
        if (!$termmeta_meta_key) {        
            $message.= "Add index key on ".$table_prefix."termmeta table column meta_key\n";
            $total_indexes+=1;
        }
        $termmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."termmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$termmeta_auto) {
            $message.= "Add autoincrement on ".$table_prefix."termmeta table column meta_id\n";
            $total_indexes+=1;
        }

        // // comment meta
        $this->query('DELETE FROM '.$table_prefix.'commentmeta WHERE meta_id = 0');
        $commentmeta_primary = $this->query("select * from information_schema.columns where table_name='".$table_prefix."commentmeta' and column_key='PRI'");
        if (!$commentmeta_primary){
            $message.= "Add primary key on ".$table_prefix."commentmeta table\n";
            $total_indexes+=1;
        }  
        $commentmeta_comment_id_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."commentmeta' and column_name='comment_id'");
        if (!$commentmeta_comment_id_key) {        
            $message.= "Add index key on ".$table_prefix."commentmeta table column comment_id\n";
            $total_indexes+=1;
        }
        $commentmeta_meta_key = $this->query("select * from information_schema.statistics where table_name='".$table_prefix."commentmeta' and column_name='meta_key'");
        if (!$commentmeta_meta_key) {        
            $message.= "Add index key on ".$table_prefix."commentmeta table column meta_key\n";
            $total_indexes+=1;
        }
        $commentmeta_auto = $this->query("select * from information_schema.columns where table_name='".$table_prefix."commentmeta' and column_name='meta_id' AND EXTRA like '%auto_increment%'");
        if (!$commentmeta_auto) {
            $message.= "Add autoincrement on ".$table_prefix."commentmeta table column meta_id\n";
            $total_indexes+=1;
        }          
        $message.= "Total indexing ".$total_indexes."\n";
        $this->logger->notice($message);
        $this->register_output($message);
        
    }

}    
