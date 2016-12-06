<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Log;
use DB;
use Exception;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var
     */
    protected $model;
    private $where;
 
    /**
     * @param App $app
     * @throws \Bosnadev\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Specify Model class name
     * 
     * @return mixed
     */
    abstract function model();
 
    /**
     * @return Model
     * @throws Exception
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
 
        if (!$model instanceof Model) {
            throw new Exception(trans('common/errors.exceptions.not-instance', ['model' => $this->model()]));
        }
 
        return $this->model = $model;
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->all();
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {  
        return $this->model->pluck($column, $key);
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null   $limit
     * @param array  $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'])
    {
        $limit = is_null($limit) ? config('repository.pagination.limit', 10) : $limit;

        return $this->model->paginate($limit, $columns);
    }

    /**   
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function findBy($column, $option)
    {
        $data = $this->model->where($column, $option)->get();

        return $data;
    }

    public function eagerLoadTrashed()
    {
        if (!is_null($this->withTrashed)) {
            $this->model->withTrashed();
        } elseif (!is_null($this->onlyTrashed)) {
            $this->model->onlyTrashed();
        }

        return $this;
    }

    public function where($conditions, $operator = null, $value = null)
    {
        if (func_num_args() == 2) {
            list($value, $operator) = [$operator, '='];
        }

        $this->where[] = [$conditions, $operator, $value];

        return $this;
    }
    
    /**
     * Save a new entity in repository
     *
     * @throws Exception
     *
     * @param array $input
     *
     * @return mixed
     */
    public function create(array $input)
    {   
        return $this->model->create($input);
    }

    /**
     * Update a entity in repository by id
     *
     * @throws Exception
     *
     * @param array $input
     * @param       $id
     *
     * @return void
     */
    public function update(array $input, $id)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($input);
        $model->save();

        return $this;
    }
    
    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Triggered inaccessible methods
     *
     * @param Method   $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->model, $method], $args);
    }

}
