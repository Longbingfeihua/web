<?php
/**
 * Created by PhpStorm.
 * User: zhangxian
 * Date: 16/12/27
 * Time: 上午10:52
 */
namespace App\Repositories\Eloquents;

use Illuminate\Support\Facades\Auth;
use App\Models\Publish as PublishModel;
use App\Repositories\InterfacesBag\Image as ImageInterface;
use App\Repositories\InterfacesBag\Video as VideoInterface;
use App\Repositories\InterfacesBag\Article as ArticleInterface;
use App\Repositories\InterfacesBag\Product as ProductInterface;
use App\Repositories\InterfacesBag\Publish as PublishInterface;

class Publish implements PublishInterface
{
    protected $module = 'publish';

    protected $image, $video, $article, $product;

    public function __construct(ImageInterface $image, VideoInterface $video, ArticleInterface $article,
                                ProductInterface $product)
    {
        $this->image = $image;
        $this->video = $video;
        $this->article = $article;
        $this->product = $product;

    }

    public function index(array $condition)
    {
        $condition = array_filter($condition, 'strlen');
        $page = isset($condition['page']) ? $condition['page'] : 1;
        $per_page_num = isset($condition['per_page_num']) ? $condition['per_page_num'] : 15;
        $publish = PublishModel::where('id', '>', '1');
        array_map(function($y) use (&$publish, $condition) {
            if (isset($condition[$y])) {
                $w = in_array($y, ['keywords', 'title']) ? '%' . $condition[$y] . '%' : $condition[$y];
                $publish = $publish->where($y, $w);
            }
        }, ['type', 'keywords', 'title', 'weight']);
        $publish = $publish->paginate($per_page_num, ['*'], 'page', $page)->toArray();

        return $publish;

    }

    public function show($id)
    {
        if (!$publish = PublishModel::find($id)) {
            return ['errorCode' => 1604];
        }
        if (file_exists(public_path($publish->path))) {
            return redirect(url($publish->path));
        }
        $publish = $this->create(['id' => $id, 'type' => $publish->type, 'tpl_id' => null, 'path' => null]);

        return redirect(url($publish->path));
    }

    public function create(array $data)
    {
        if (!$id = $data['id']) {
            return ['errorCode' => 1600];
        }
        if ((!$type = $data['type']) || !isset($this->{strtolower($data['type'])})) {
            return ['errorCode' => 1601];
        }
        $detail = $this->$type->show($id);
        $tpl = $data['tpl_id'] ? : 'tpl.default.' . $type . '_detail';
        $html = view($tpl, ['detail' => $detail])->render();
        $params = [
            'title'     => $detail['title'],
            'keywords'  => '',
            'index_pic' => $detail['index_pic'],
            'tags'      => '',
            'user_id'   => Auth::id()
        ];
        if ($publish = PublishModel::where('cid', $id)->first()) {
            $path = $publish->path;
            file_put_contents(public_path($path), $html);
            $publish = $this->update($publish->id, $params);
        } else {
            if (!$path = $this->checkDir($data['path'] ? : $type . '/' . Date('Y/m/d') . '/')) {
                return ['errorCode' => 1602];
            }
            $filename = microtime(1) * 10000 . '.html';
            $path = $path . $filename;
            file_put_contents(public_path($path), $html);
            $params['cid'] = $id;
            $params['type'] = $type;
            $params['path'] = $path;
            if (!$publish = PublishModel::create($params)) {
                return ['errorCode' => 1603];
            }
            event('log', [[$this->module, 'c', $publish]]);
        }

        return $publish;
    }

    public function update($id, array $data)
    {
        if (!$before = PublishModel::find($id)) {
            return ['errorCode' => 1604];
        }
        $data = array_intersect_key($data, array_flip(['title', 'keywords', 'index_pic', 'tags', 'user_id', 'path']));
        if (!PublishModel::where('id', $id)->update($data)) {
            return ['errorCode' => 1605];
        }
        $after = PublishModel::find($id)->toArray();
        event('log', [[$this->module, 'u', ['before' => $before, 'after' => $after]]]);

        return $after;
    }

    public function delete($id)
    {
        if (!$publish = PublishModel::find($id)) {
            return ['errorCode' => 1604];
        }
        $path = $publish->path;
        if (!$publish->delete()) {
            return ['errorCode' => 1606];
        }
        unlink(public_path($path));
        event('log', [[$this->module, 'd', $publish]]);

        return $publish;
    }

    protected function checkDir($path)
    {
        $realpath = public_path($path);
        if (!is_dir($realpath)) {
            @mkdir($realpath, 0775, 1);
        }

        return is_dir($realpath) && is_writeable($realpath) ? $path : false;
    }
}