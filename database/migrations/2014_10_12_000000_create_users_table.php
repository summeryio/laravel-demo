<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * 当我们运行迁移时，up 方法会被调用；
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); // Email 验证时间，可以为空
            $table->string('password');
            $table->rememberToken(); // 保存『记住我』
            $table->timestamps(); // 创建了一个 created_at 和一个 updated_at 字段
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * 当我们回滚迁移时，down 方法会被调用。
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
