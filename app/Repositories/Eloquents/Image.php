<?php
/**
 * Created by PhpStorm.
 * User: zhangxian
 * Date: 16/6/12
 * Time: 下午3:22
 */
namespace App\Repositories\Eloquents;

use Auth;
use App\Models\Image as ImageModel;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image as ImageContarct;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repositories\InterfacesBag\Image as ImageInterface;
class Image implements ImageInterface{
    protected $module = 'image';

    public function index($condition = []){
        return ImageModel::leftJoin('administrators','administrators.id','=','images.user_id')->leftJoin
        ('image_sorts','image_sorts.id','=','images.sort_id')->select('images.*','image_sorts.name as sort_name',
            'administrators.username')->orderBy('images.created_at','DESC')->get()->groupBy('sort_id');
    }
    public function show($id){
        return ImageModel::findOrFail($id);
    }

    //图片上传
    public function create(UploadedFile $file,array $params = []){
        $path = $params['path'] ? $params['path'] : 'images/origin/'.Date('Y/m/d');
        if(!$this->checkDir(public_path($path))){
            event('log',[[$this->module,'c','mkdir@'.public_path($path).'failed!',0]]);

            return Response::error(1100);
        }
        $basename = microtime(true) * 10000;
        $name  = $basename.'.'.$file->guessExtension();
        $file->move($path,$name);
        $thumbPath = $params['thumb_path'] ? $params['thumb_path'] : 'images/thumb/'.Date('Y/m/d');
        if(!$this->checkDir(public_path($thumbPath))){
            event('log',[[$this->module,'c','mkdir@'.public_path($thumbPath).'failed!',0]]);

            return Response::error(1101);
        }
        ImageContarct::make($path.'/'.$name)->resize($params['thumb_width'] ? $params['thumb_width'] : 320,null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($thumbPath.'/'.$name);
        $imageInfo = [
            'name'=> $params['name'] ? $params['name'] : "新建图像文件",
            'sort_id'=>$params['sort_id'] ? $params['sort_id'] : 3, //1系统,2截图,3普通
            'path' => $path.'/'.$name,
            'thumb'=>$thumbPath.'/'.$name,
            'user_id'=>Auth::id(),
        ];
        if($info = ImageModel::create($imageInfo)){
            event('log',[[$this->module,'c',$info]]);

            return Response::push($info);
        }
    }

    //创建目录
    protected function checkDir($path){
        if(!is_dir($path)){
            @mkdir($path,0775,1);
        }

        return is_dir($path) && is_writeable($path);
    }

    //图片删除
    public function delete($id){
        if(!$media = ImageModel::where('id',$id)->first()){
            return Response::error(1102);
        }
        if(ImageModel::destroy($id)){
            @unlink(public_path($media->path));
            @unlink(public_path($media->thumb));
            event('log',[[$this->module,'d',$media]]);

            return Response::push(['id'=>$id]);
        }

        return Response::error(1103);
    }
}