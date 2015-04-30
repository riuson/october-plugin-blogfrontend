<?php

namespace Riuson\BlogFrontEnd\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class PostViewer extends \RainLab\Blog\Components\Post
{

    public function componentDetails()
    {
        return [
            'name' => 'riuson.blogfrontend::lang.post_viewer.name',
            'description' => 'riuson.blogfrontend::lang.post_viewer.description'
        ];
    }

    public function defineProperties()
    {
        $result = parent::defineProperties();
        $result['pageEditor'] = [
            'title' => 'riuson.blogfrontend::lang.post_viewer.pageeditor_title',
            'description' => 'riuson.blogfrontend::lang.post_viewer.pageeditor_description',
            'type' => 'dropdown',
            'default' => ''
        ];
        return $result;
    }

    public function getPageEditorOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        parent::onRun();

        $this->post_user_name = null;
        $this->pageEditor = null;
        $allow_editing = false;

        if ($this->post != null) {
            $frontend_user = $this->post->frontend_user->first();



            if ($frontend_user != null) {
                $this->post_user_name = $frontend_user->name;

                $user = \Auth::getUser();

                if ($user != null) {
                    if ($frontend_user->getKey() == $user->getKey()) {
                        $allow_editing = true;
                    }
                }

                if ($frontend_user->key != null) {
                    if ($frontend_user->key->primaryCharacter() != null) {
                        $this->post_user_name = $frontend_user->key->primaryCharacter()->characterName;
                    }
                }

                if ($allow_editing) {
                    $this->pageEditor = $this->controller->pageUrl($this->property('pageEditor'));
                }
            }
        }

        if ($allow_editing == false) {
            if (\Riuson\EveApiUser\Classes\AccessControl::userCanViewPost($this->post) == false) {
                $this->post = $this->page['post'] = null;
                return \Redirect::guest("/");
            }
        }
    }
}