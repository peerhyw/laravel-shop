<?php

namespace App\Admin\Controllers;

/*
use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
*/

use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UsersController extends Controller
{
    //use HasResourceActions;
    use ModelForm;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        /*return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());*/
        //Admin::content() 会根据回调函数来渲染页面，它会自动渲染页面顶部、菜单、底部等公共元素，而我们可以调用 $content 的方法在页面上添加元素来设置不同页面的内容。
        return Admin::content(function (Content $content){
            $content->header('用户列表');
            //$content->description('description');

            //$content->body() 用来渲染页面的核心内容，可以接受任何可字符串化的对象作为参数，比如字符串、Laravel 的视图等
            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    /*public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }*/

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    /*public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }*/

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    /*public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }*/

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        /*$grid = new Grid(new User);

        $grid->id('Id');
        $grid->name('Name');
        $grid->email('Email');
        $grid->password('Password');
        $grid->remember_token('Remember token');
        $grid->email_verified('Email verified');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        return $grid;*/
        //根据回调函数，在页面上用表格的形式展示用户记录
        return Admin::grid(User::class,function (Grid $grid){
            //创建一个列名为id的列，内容是用户的id字段，并且可以在前段页面点击排序
            $grid->id('ID')->sortable();
            //创建一个列名为用户名的列，内容是用户的name字段。下面的email()和created_at()同理
            $grid->name('用户名');
            $grid->email('邮箱');
            $grid->email_verified('已验证邮箱')->display(function ($value){
                return $value ? '是' : '否';
            });
            $grid->created_at('注册时间');

            //不在页面显示 新建 按钮 因为我们不需要在后台新建用户
            $grid->disableCreateButton();

            $grid->actions(function ($actions){
                //不在每一行后面展示查看按钮
                $actions->disableView();
                //不在每一行后面展示删除按钮
                $actions->disableDelete();
                //不在每一行后面展示编辑按钮
                $actions->disableEdit();
            });

            $grid->tools(function ($tools){
                //禁用批量删除按钮
                $tools->batch(function ($batch){
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    /*protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->password('Password');
        $show->remember_token('Remember token');
        $show->email_verified('Email verified');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }*/

    /**
     * Make a form builder.
     *
     * @return Form
     */
    /*protected function form()
    {
        $form = new Form(new User);

        $form->text('name', 'Name');
        $form->email('email', 'Email');
        $form->password('password', 'Password');
        $form->text('remember_token', 'Remember token');
        $form->switch('email_verified', 'Email verified');

        return $form;
    }*/
}
