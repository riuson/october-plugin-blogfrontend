<?php namespace Riuson\EveApiUser\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateBlogCategoryGroupsTable extends Migration
{

    public function up()
    {
        Schema::create('riuson_blogfrontend_blog_category_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riuson_blogfrontend_blog_category_groups');
    }

}
