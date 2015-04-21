<?php
namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\Blog\Models\Post as PostModel;
use RainLab\Blog\Models\Category as CategoryModel;
use Riuson\BlogFrontEnd\Models\BlogPostUser as BlogPostUserModel;
use Carbon\Carbon;

// class PostEditor extends ComponentBase
class PostEditor extends \RainLab\Blog\Components\Post
{

    public function componentDetails()
    {
        return [
            'name' => 'Post Editor',
            'description' => 'Front-End post editor.'
        ];
    }

    public function defineProperties()
    {
        $result = parent::defineProperties();
        $result['redirectOnPost'] = [
            'title' => 'Post redirect',
            'description' => 'Redirect after successful posting',
            'type' => 'dropdown',
            'default' => 'blog'
        ];
        return $result;
    }

    public function getRedirectOnPostOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();
        $this->errorText = null;

        $this->post_slug = null;
        $this->post_title = null;
        $this->post_excerpt = null;
        $this->post_content = null;
        $this->selected_categories = null;

        if ($this->post != null) {
            $this->post_slug = $this->post->slug;
            $this->post_title = $this->post->title;
            $this->post_excerpt = $this->post->excerpt;
            $this->post_content = $this->post->content;

            $categories = array();

            foreach ($this->post->categories as $category) {
                array_push($categories, $category->getKey());
            }

            $this->selected_categories = $categories;
        }

        $this->categories = CategoryModel::orderBy('name', 'asc')->get();
    }

    public function onSubmit()
    {
        $success = false;

        $this->post_slug = $slug = post('input-slug', '');
        $this->post_title = $title = post('input-title', '');
        $this->post_excerpt = $excerpt = post('input-excerpt', '');
        $this->post_content = $content = post('input-content', '');
        $this->selected_categories = $categories = post('categories', '');

        if (empty($slug)) {
            $this->post_slug = $slug = \Str::slug($title);
        }

        if ($this->post == null) {
            return $this->createNewPost($slug, $title, $excerpt, $content, $categories);
        } else {
            return $this->updateExistingPost($slug, $title, $excerpt, $content, $categories);
        }
    }

    private function createNewPost($slug, $title, $excerpt, $content, $categories)
    {
        if (! empty($slug)) {
            if ($this->isSlugUnique($slug)) {
                $post = new PostModel();
                $post->user_id = null;
                $post->title = $title;
                $post->slug = $slug;
                $post->excerpt = $excerpt;
                $post->content = $content;
                $post->published_at = Carbon::now('UTC');
                $post->published = 1;

                if ($post->save()) {
                    $postuser = new BlogPostUserModel();
                    $postuser->user_id = \Auth::getUser()->getKey();
                    $postuser->post_id = $post->getKey();

                    if ($postuser->save()) {
                        $this->updateCategoriesForPost($post->getKey(), $categories);
                        $success = true;
                        $redirect = $this->property('redirectOnPost');
                        return \Redirect::to($redirect);
                    } else {
                        $this->errorText = 'Assign user to post failed.';
                    }
                } else {
                    $this->errorText = 'Saving post failed.';
                }
            } else {
                $this->errorText = 'Slug not unique.';
            }
        } else {
            $this->errorText = 'Slug is empty.';
        }
    }

    private function updateExistingPost($slug, $title, $excerpt, $content, $categories)
    {
        if (($this->post->slug != $slug) and (! $this->isSlugUnique($slug))) {
            $this->errorText = 'Slug not unique.';
            return;
        }

        $this->post->slug = $slug;
        $this->post->title = $title;
        $this->post->excerpt = $excerpt;
        $this->post->content = $content;

        if ($this->post->save()) {
            $this->updateCategoriesForPost($this->post->getKey(), $categories);
            $redirect = $this->property('redirectOnPost');
            return \Redirect::to($redirect);
        }
    }

    private function isSlugUnique($slug)
    {
        $count = PostModel::whereSlug($slug)->count();
        return ($count == 0);
    }

    private static function updateCategoriesForPost($postID, $categoriesIDs)
    {
        if ($postID != null) {
            if (is_array($categoriesIDs) && count($categoriesIDs) > 0) {
                // remove obsolete
                \DB::table('rainlab_blog_posts_categories')->whereNotIn('category_id', $categoriesIDs)
                    ->where('post_id', '=', $postID)
                    ->delete();

                // add new
                foreach ($categoriesIDs as $categoryID) {
                    $exists = \DB::table('rainlab_blog_posts_categories')->where('post_id', '=', $postID)
                        ->where('category_id', '=', $categoryID)
                        ->first();

                    if ($exists == null) {
                        \DB::insert('insert into rainlab_blog_posts_categories (post_id, category_id) values (?, ?);', [
                            $postID,
                            $categoryID
                        ]);
                    }
                }
            } else {
                // remove all
                \DB::table('rainlab_blog_posts_categories')->where('post_id', '=', $postID)->delete();
            }
        }
    }
}
