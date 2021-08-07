<?php 
namespace App\Core\Repositories;

use App\Models\News;

class InnerNews extends BaseRepository {

    protected $model;

    public function __construct(){
        $this->model  = new News();
    }

    public function createNewsWeekend($user_id, $news){
        if($this->checkNewsUnique($news['link'])){
            return false;
        }else{
            return $this->create([
                'user_id' => $user_id,
                'pub_date' => $news['pub_date'],
                'title'   => $news['title'],
                'description' => $news['description'],
                'image'   => $news['image'],
                'content' => $news['content'],
                'link'    => $news['link'],
                'video'   => $news['video'],
                'name_link'    => $news['name_link'],
                'kind'    => $news['kind'],
                'news_order'    => $news['news_order'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function checkNewsUnique($link){
        return $this->model->whereLink($link)->exists();
    }

    public function getDescription($id){
        return $this->model->where('id', $id)->select('description')->first();
    }
    
    public function getContent($id){
        return $this->model->where('id', $id)->select('content')->first();
    }

    public function totalNewsForDate($date){
        return $this->model->where('pub_date', $date)->count();
    }

    public function getDetailNews($id){
        return $this->model->with(['comments' => function($q) {
                                $q->with('user');
                            }])->find($id);
    }

    public function updateParams($params, $condition) {
        return $this->model->where($condition)->update($params);
    }

    public function checkLinkDuplicate($link) {
        return $this->model->where('link', $link)->whereIn('status', [0,1,2])->count();
    }

}

?>