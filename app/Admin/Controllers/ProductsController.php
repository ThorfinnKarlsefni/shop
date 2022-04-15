<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));
        $grid->column('image', __('Image'));
        $grid->column('on_sale')->display(function($value){
            return $value ? 'Y' : 'N';
        });
        $grid->column('rating', __('Rating'));
        $grid->column('sold_count', __('Sold count'));
        $grid->column('review_count', __('Review count'));
        $grid->column('price', __('Price'));

        $grid->actions(function($actions){
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->tools(function($tools){
            $tools->batch(function($batch){
                $batch->disableDelete();
            });
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('title')->rules('requried');
        $form->quill('description')->rules('requried');
        $form->image('image')->rules('requried|image');
        $form->radio('on_sale')->options(['1' => 'Y','0' => 'N'])->default(0);
        $form->decimal('rating')->default(5.00);
        //一对多关联模型
        $form->hasMany('skus',function(Form\NestedForm $form){
            $form->text('title')->rules('required');
            $form->text('description')->rules('required');
            $form->text('prcie')->rules('required|min:0.01|numeric');
            $form->text('stock')->rules('required|min:0|integer');
        });
        //事件监听
        $form->saving(function(Form $form){
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price') ?: 0;
        });

        return $form;
    }
}
