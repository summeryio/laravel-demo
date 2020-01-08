<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct() {
        // 身份认证，未登录时访问相关页面会重定向到login
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
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

        // Auth::login($user); // 注册成功自动登录
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        
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


    public function index() {
        $users = User::paginate(10);
        
        return view('users.index', compact('users'));
    }

    public function destroy(User $user) {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '删除用户成功！');
        return back();
    }


    // 发送激活邮件
    public function sendEmailConfirmationTo($user) {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@qq.com';
        $name = 'summer';
        $to = $user->email;
        $subject = '感谢注册 Weibo 应用！请确认你的邮箱。';

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 激活邮件确认页面
    public function confirmEmail($token) {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
