<?php

return [
    'plugin' => [
        'name' => 'Blog Front-End',
        'description' => 'Front-End interface for RainLab.Blog.',
    ],
    'backend' => [
        'field_groups_label' => 'Groups',
        'field_groups_comment_above' => 'Specify which groups can view posts in this category.',
        'field_groups_tab_name' => 'Groups',
    ],
    'categories_viewer' => [
        'name' => 'Categories Viewer',
        'description' => 'Lists categories with access rights',
    ],
    'post_editor' => [
        'name' => 'Post Editor',
        'description' => 'Front-End post editor.',
        'redirectonpost_title' => 'Post redirect',
        'redirectonpost_description' => 'Redirect after successful posting',
        'error_user_assign_failed' => 'Assign user to post failed.',
        'error_save_post_failed' => 'Saving post failed.',
        'error_slug_not_unique' => 'Slug not unique.',
        'error_slug_is_empty' => 'Slug is empty.',
        'error_editing_forbidden' => 'Only author can edit this blog post.',
        'title' => 'Title',
        'title_placeholder' => 'Title for the post',
        'slug' => 'Slug',
        'slug_placeholder' => 'Slug for the post',
        'excerpt' => 'Excerpt',
        'content' => 'Content',
        'submit' => 'Submit',
    ],
    'posts_viewer' => [
        'name' => 'Posts list',
        'description' => 'List of posts accessible for user',
        'showpager_title' => 'Show pager',
        'showpager_description' => 'Show page navigation',
        'showpager_option_show' => 'Show',
        'showpager_option_hide' => 'Hide',
        'posted' => 'Posted',
        'by_user' => 'by',
        'in_category' => 'in',
        'published_at' => 'at',
        'previous' => 'Prev',
        'next' => 'Next',
    ],
    'post_viewer' => [
        'name' => 'Show post',
        'description' => 'Show one post',
        'pageeditor_title' => 'Editor page',
        'pageeditor_description' => 'Page for post editing',
        'posted' => 'Posted',
        'by_user' => 'by',
        'in_category' => 'in',
        'published_at' => 'at',
        'edit' => 'Edit',
    ],
];
