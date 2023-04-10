<?php

namespace App\Http\Livewire;

use App\Exceptions\InvalidClassException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Datatable extends Component
{
    use WithPagination;

    protected $listners = ['refresh' => '$refresh'];

    protected $queryString = ['perPage', 'search'];

    public $model;
    public $filters;
    public $columns;
    public $uniqueId;
    public $search = null;
    public $perPage = 10;

    protected $rules = [
        'perPage'  => 'nullable|integer',
        'search'   => 'nullable|string',
    ];

    /**
     * @param string|Builder $model Pass model or query builder
     *
     * @return void
     */
    public function mount(string|Builder $model, array $columns, array $filters = [], $uniqueId = null)
    {
        $this->model = $model;
        $this->filters = $filters;
        $this->uniqueId = $uniqueId ?? Str::random(10);

        $this->encryptValues();
    }

    /**
     * Verify if a class is an eloquent model.
     *
     * @param object $model
     *
     * @throws \App\Exceptions\InvalidClassException
     */
    public function verifyIsModel($model): bool
    {
        if (!is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
            throw new InvalidClassException(sprintf('Class %s is not a model', get_class($model)), 1);
        }

        return true;
    }

    public function BuildPagination()
    {
        $this->validate();
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

        //create closure with filters to be applied to model
        $searchFilter = function ($query) use ($model) {
            foreach ($this->columns as $column) {
                if (!array_key_exists('columnName', $column)) {
                    if (!array_key_exists('property', $column) || empty($column['property'])) {
                        break;
                    }
                }

                //get table name from either DatabaseBuilder or EloQuent model
                $table = $model->getModel()->getTable() ?? $model?->getQuery()->getModel()->getTable();

                if (array_key_exists('relation', $column) && !empty($column['relation'])) {
                    //filter relation
                    $query = call_user_func_array([$query, 'orWhereRelation'], [$column['relation'], $column['columnName'] ?? $column['property'], 'LIKE', "%$this->search%"]);
                } else {
                    //filter column
                    $query = call_user_func_array([$query, 'orWhere'], [$table.'.'.($column['columnName'] ?? $column['property']), 'LIKE', "%$this->search%"]);
                }
            }

            return $query;
        };

        return $model = call_user_func_array([$model, 'where'], [$searchFilter]);
    }

    public function encryptValues()
    {
        $this->filters = Crypt::encryptString(serialize($this->filters));
        $this->model = Crypt::encryptString(serialize($this->model));
    }

    public function decryptValues()
    {
        $this->filters = unserialize(Crypt::decryptString($this->filters));
        $this->model = unserialize(Crypt::decryptString($this->model));
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
        $this->decryptValues();
        $collection = $this->BuildPagination();
        $this->encryptValues();

        return view('livewire.datatable', [
            'collection' => $collection,
        ]);
    }
}
