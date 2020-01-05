<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function create() {
        return view('users.create');
    }

    // $user 会匹配路由片段中的 {user}
    public function show(User $user) {
        // 将用户对象 $user 通过 compact 方法转化为一个关联数组，并作为第二个参数传递给 view 方法，将数据与视图进行绑定。
        return view('users.show', compact('user'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required | max:50',
            'email' => 'required | email | unique:users | max:255',
            'password' => 'required | confirmed | min:2'
        ]);
        
        return;
    }
}
