<?php
namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\Blog\Models\Post as PostModel;
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

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();
    }

    public function onSubmit()
    {
        $redirect = $this->property('redirectOnPost');
        $success = false;
        $errorText = '';

        $originalSlug = post('input-original-slug', '');
        $excerpt = post('input-excerpt', '');
        $content = post('input-content', '');
        $title = post('input-title', '');
        $slug = post('input-slug', '');

        if (empty($originalSlug)) {
            $now = Carbon::now('UTC');

            if (!empty($slug)) {
                if ($this->isSlugUnique($slug)) {
                    $post = new PostModel();
                    $post->user_id = null;
                    $post->title = $title;
                    $post->slug = $slug;
                    $post->excerpt = $excerpt;
                    $post->content = $content;

                    $post->published_at = $now;
                    $post->published = 1;

                    if ($post->save()) {
                        $postuser = new BlogPostUserModel();
                        $postuser->user_id = \Auth::getUser()->getKey();
                        $postuser->post_id = $post->getKey();

                        if ($postuser->save()) {
                            $success = true;
                            return \Redirect::to($redirect);
                        } else {
                            $errorText = 'Assign user to post failed.';
                        }
                    } else {
                        $errorText = 'Saving post failed.';
                    }
                } else {
                    $errorText = 'Slug not unique.';
                }
            } else {
                $errorText = 'Slug is empty.';
            }
        }

        return [
            'success' => $success,
            'errorText' => $errorText
        ];
    }

    private function isSlugUnique($slug)
    {
        $count = PostModel::whereSlug($slug)->count();
        return ($count == 0);
    }

    public function onCheckTitle()
    {
        $title = post('input-title', '');

        $titleValid = false;
        $slugValid = false;
        $slug = '';

        if (! empty($title)) {
            $titleValid = true;
            $slug = \Str::slug($title);

            if (! empty($slug)) {
                $unique = $this->isSlugUnique($slug);

                if ($unique) {
                    $slugValid = true;
                }
            }
        }

        return [
            'titleValid' => $titleValid,
            'slug' => $slug,
            'slugValid' => $slugValid
        ];
    }

    public function onCheckSlug()
    {
        $slug = post('input-slug', '');
        $slugValid = false;

        if (! empty($slug)) {
            $unique = $this->isSlugUnique($slug);

            if ($unique) {
                $slugValid = true;
            }
        }

        return [
            'slugValid' => $slugValid
        ];
    }
}
