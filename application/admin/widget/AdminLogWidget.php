<?php
namespace app\admin\widget;

use think\Controller;

class AdminLogWidget extends Controller
{
    public function search(){
        $data = [
            [
                lang('username') => [
                    'html'          => 'input',
                    'default_value' => input('get.username__like',''),
                    'type'          => 'text',
                    'name'          => 'username__like',
                ],
                lang('url') => [
                    'html'          => 'input',
                    'default_value' => input('get.url__like',''),
                    'type'          => 'text',
                    'name'          => 'url__like',
                ],
                lang('title') => [
                    'html'          => 'input',
                    'default_value' => input('get.title__like',''),
                    'type'          => 'text',
                    'name'          => 'title__like',
                ],
                lang('ip') => [
                    'html'          => 'input',
                    'default_value' => input('get.ip__like',''),
                    'type'          => 'text',
                    'name'          => 'ip__like',
                ],
                'Button' => [
                    'search','reset'
                ]
            ],
        ];
        $this->assign('data',$data);
        return $this->fetch('widget/search');
    }

}