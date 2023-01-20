<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class Datatable extends Component
{
    use WithPagination;
    protected $listners = ['refresh' => '$refresh'];
    public $model, $filters, $columns, $uniqueId, $search = null;
    public int $perPage = 10;

    /**
     * @param string|Builder $model Pass model or query builder
     * @return void
     */
    public function mount(string|Builder $model, array $columns,array $filters = [], $uniqueId = null)
    {
        $this->model = $model;
        $this->filters = $filters;
        $this->uniqueId = $uniqueId ?? Str::random(10);
    }

    /**
     * Verify if a class is an eloquent model
     *
     * @param Object $model
     * @throws Exception
     * @return bool
     */
    public function verifyIsModel($model)
    {
        if (!is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
            throw new \Exception(sprintf("Class %s is not a model", $model), 1);
        }
        return 1;
    }

    public function BuildPagination()
    {
        $model = app()->make($this->model);
        $this->verifyIsModel($model);

        foreach ($this->filters as $filter) {
            $model = call_user_func_array([$model, $filter['name']], $filter['arguments'] ?? []);
        }

        $model = $this->addSearchFilter($model);
        
        return $model->paginate($this->perPage, ['*'], $this->uniqueId);
    }

    public function addSearchFilter($model)
    {
        if ($this->search == null || empty($this->search)) {
            return $model;
        }

        $searchFilter = function ($query)
        {
            foreach ($this->columns as $column) {
                if (array_key_exists('property', $column) && !empty($column['property'])) {
                    
                    //include count for id
                    $query = call_user_func_array([$query, 'orWhere'],["id" , 'LIKE' , "%$this->search%"]);

                    if (array_key_exists('relation', $column)  && !empty($column['relation'])) {
                        $query = call_user_func_array([$query, 'orWhereRelation'],[$column['relation'],$column['property'] ?? 'id' , 'LIKE' , "%$this->search%"])->toSQL();
                    }else {
                        $query = call_user_func_array([$query, 'orWhere'],[$column['property'] ?? 'id' , 'LIKE' , "%$this->search%"]);
                    }
                }
            }
                
            return $query;
        };

        return $model = call_user_func_array([$model, 'where'], [$searchFilter]);
    }

    public function updatedPerPage()
    {
        $this->resetPage($this->uniqueId);
    }

    public function updatedSearch()
    {
        $this->resetPage($this->uniqueId);
    }

    public function paginationView()
    {
        return 'components.datatable-pagination-links-view';
    }
    
    public function render()
    {
        $collection = $this->BuildPagination();
        return view('livewire.datatable', [
            'collection' => $collection
        ]);
    }
}
