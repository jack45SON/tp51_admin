<?php

namespace app\admin\controller;

use app\admin\model\Attach;
use think\Controller;

define("DS", DIRECTORY_SEPARATOR);

class Upload extends Controller
{
    public function image()
    {
        return json($this->_uploadImage('admin'));
    }


    private function _uploadImage($path, $size = 1024 * 1024 * 20, $ext = 'jpg,png,gif,jpeg')
    {
        //接收参数
        $images = request()->file('file');
        //计算md5和sha1散列值，TODO::作用避免文件重复上传
        $md5 = $images->hash('md5');
        $sha1 = $images->hash('sha1');
        \Log::record('上传文件:'.json_encode($images));
        //判断图片文件是否已经上传
        $Model = new Attach();
        $img = $Model->where(['md5' => $md5, 'sha1' => $sha1])->find();

        if ($img) {
            $data['thumb'] = $this->thumb($img['path'], array(array(100, 100), array(800, 800), array(80, 80)));
            return [
                'status'    => 1,
                'message'   => lang('upload_success'),
                'data'      => [
                    'img_id'    =>  $img['id'],
                    'img_url'   =>  $img['path']
                ]
            ];
        } else {
            // 移动到框架应用根目录下
            $imgPath = 'public' . DS . 'uploads' . DS . 'img' . DS . $path;
            try {
                $info = $images->validate(['size' => $size, 'ext' => $ext])->move(\Env::get('root_path') . $imgPath);
            } catch (\Exception $e) {
                return ['status' => -1, 'message' => $e->getMessage()];
            }


            $path = DS . 'uploads' . DS . 'img' . DS . $path . DS . $info->getSaveName();

           $data['thumb'] = $this->thumb($path, array(array(100, 100), array(800, 800), array(80, 80)));

            $data = [
                'domain_name'   => request()->root(true),
                'path'          => $path,
                'md5'           => $md5,
                'sha1'          => $sha1
            ];
            //将图片存入数据库，防止重复上传
            try {
                $img_id = $Model->insertUpdate($data, 'id', false);
            } catch (\Exception $e) {
                return ['status' => -1, 'message' => $e->getMessage()];
            }
            if ($img_id) {
                return [
                    'status'        => 1,
                    'message'       => lang('upload_success'),
                    'data'          => [
                        'img_id'    => $data['domain_name'].$img_id,
                        'img_url'   => $path
                    ]
                ];
            } else {
                return ['status' => -1, 'message' => '写入数据库失败'];
            }
        }
    }

    public function thumb($imgUrl, $thumb_array = array(array(100, 100)))
    {
        $imgUrl = substr($imgUrl, 1);
        $imgName = basename($imgUrl);
        $imgPath = dirname($imgUrl);
        if ($thumb_array) {
            $thumbPath = array();
            $image = \think\Image::open('' . $imgUrl . '');
            $width = $image->width();
            $height = $image->height();
            foreach ($thumb_array as $k => $v) {
                $thumbPath[$k + 1] = "";

                $w = $v[0];
                $h=$w*$height/$width;
                $thumbPath[$k + 1] = $imgPath . '/thumb_' . $v[0] . '_' . $v[1] . '_' . $imgName;
                //判断当前生成的缩略图是否存在

                if (file_exists($thumbPath[$k + 1])) {
                    continue;
                }

                //常量，标识缩略图等比例缩放类型
                // const THUMB_SCALING   = 1;
                //常量，标识缩略图缩放后填充类型
                // const THUMB_FILLED    = 2;
                //常量，标识缩略图居中裁剪类型
                // const THUMB_CENTER    = 3;
                //常量，标识缩略图左上角裁剪类型
                // const THUMB_NORTHWEST = 4;
                //常量，标识缩略图右下角裁剪类型
                // const THUMB_SOUTHEAST = 5;
                //常量，标识缩略图固定尺寸缩放类型
                //const THUMB_FIXED     = 6;
                $image->thumb($w, $h, \Think\Image::THUMB_SCALING)->save('' . $thumbPath[$k + 1] . '');
            }

        }
    }

    public function uploadBase64(){
        if(request()->isPost() && request()->isAjax()){
            $param = input('post.');
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $param['base64'], $result);
            $type = $result[2];
            // 移动到框架应用根目录下
            $path =  'uploads' . DS . 'img' . DS . 'feedback'.DS.time().'.'.$type;
            $imgPath = DS . $path;
            //  创建将数据流文件写入我们创建的文件内容中
            file_put_contents($path, base64_decode(str_replace($result[1], '', $param['base64'])));

            return $imgPath;
        }

    }
}