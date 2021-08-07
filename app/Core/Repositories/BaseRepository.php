<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Contract\BaseRepositoryInterface;
use App\Core\Traits\UploadTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

class BaseRepository implements BaseRepositoryInterface {

    use UploadTable;
    
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $attributes)
    {
        return $this->model->update($attributes);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        try{
            return $this->model->delete();
        }catch(Exception $e){
            throw new HttpResponseException(response()->json($e->getMessage()), 422);
        }
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function findBy(array $data)
    {
        return $this->model->where($data)->get();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function findOneBy(array $data)
    {
        return $this->model->where($data)->first();
    }

    /**
     * @param array $data
     */
    public function findOneByOrFail(array $data)
    {
        return $this->model->where($data)->firstOrFail();
    }

    /**
     * @param $id
     */
    public function findOneOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Paginate arrays
     * @param array $data
     * @param int $perPage
     */
    public function paginateArrayResults(array $data, int $perPage = 20)
    {
        $page = Input::get('page', 1);
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_values(array_slice($data, $offset, $perPage, true)),
            count($data),
            $perPage,
            $page,
            [
                'path' => app('request')->url(),
                'query' => app('request')->query()
            ]
        );
    }

    public function getAllWithPaginate($filter = [], $with="", $pageSize = 20, $orderby = ['created_at' => 'DESC'])
	{
      $data_return = $this->model;
      $no_filter_more = false;
		if ( ! empty($filter))
		{
			foreach ($filter as $key => $value)
			{
				if ($value == ''){
					unset($filter[$key]);
				}

				// Nếu là aray quy định
				if (is_array($value)) {
					$op     = array_get($value, 'operator');
					$val    = array_get($value, 'value');
					$column = $key;
					$data_return = $data_return->where($column, $op, $val); 
					$no_filter_more = true;
				} else {
					$data_return = $data_return->where($key,$value);
				}
			}
		}

		if(!empty($orderby)){
			foreach ($orderby as $key => $value) {
				$data_return = $data_return->orderby($key,$value);
			}
		}

		if($with != ""){
			$data_return->with($with);
		}

		return $data_return->paginate($pageSize);
	}
}