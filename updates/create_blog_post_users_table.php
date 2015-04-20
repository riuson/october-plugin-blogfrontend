<?php namespace Riuson\BlogFrontEnd\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateBlogPostUsersTable extends Migration
{

    public function up()
    {
        Schema::create('riuson_blogfrontend_blog_post_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('post_id')->unsigned()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riuson_blogfrontend_blog_post_users');
    }

}
