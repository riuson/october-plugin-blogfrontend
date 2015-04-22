<?php

namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;

class PostViewer extends \RainLab\Blog\Components\Post
{

    public function componentDetails()
    {
        return [
            'name' => 'Post Viewer',
            'description' => 'Frontend single post viewer'
        ];
    }

    public function onRun()
    {
        parent::onRun();

        $this->post_user_name = null;

        if ($this->post != null) {
            $frontend_user = $this->post->frontend_user->first();

            if ($frontend_user != null) {
                $this->post_user_name = $frontend_user->name;

                if ($frontend_user->key != null) {
                    if ($frontend_user->key->primaryCharacter() != null) {
                        $this->post_user_name = $frontend_user->key->primaryCharacter()->characterName;
                    }
                }
            }
        }
    }
}