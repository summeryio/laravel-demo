<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function __construct() {
        // 身份认证，未登录时访问相关页面会重定向到login
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store']
        ]);

        // 只让未登录用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    
    
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
            'email' => 'required | email | unique:users',
            'password' => 'required | confirmed | min:2'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Auth::login($user); // 注册成功自动登录
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user) {
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request) {
        $this->authorize('update', $user);
        
        $this->validate($request, [
            'name' => 'required | max:50',
            'password' => 'nullable | confirmed | min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }
}
