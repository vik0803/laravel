<?php

namespace App\Services;
use Illuminate\Http\Request;

class DataTable
{
    /**
     * Single or Multiple DataTables on one page.
     *
     * @var string
     */
    protected $request;
    protected $table;
    protected $tables = [];

    /**
     * Creates new instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setup($model, $table, $options, $internal = null)
    {
        $this->setTable($table);
        $this->createTable($table, $options);

        $count = $model->count();
        $this->setOption('size', ($count <= 100 ? 'small' : ($count <= 1000 ? 'medium' : 'large')));
        $columnsData = $this->getColumnsData();

        foreach ($columnsData['aggregates'] as $aggregate) {
            $model = $model->with($aggregate['aggregate']);
        }

        if ($this->request->ajax()) {
            $this->setOption('ajax', true);
            if ($internal) {
                $this->setOption('updateTable', true);
            } else {
                $this->setOption('reloadTable', true);
            }
            $this->setOption('draw', (int)$this->request->input('draw', 1));
            $this->setOption('recordsTotal', $count);

            $model = $model->select($columnsData['columns']);

            $column = $this->request->input('columns.' . $this->request->input('order.0.column') . '.data', $columnsData['orderByColumn']);
            $dir = $this->request->input('order.0.dir', $this->getOption('order'));
            $model = $model->orderBy($column, $dir);

            if ($this->request->input('search.value')) {
                $this->setOption('search', true);

                $model = $model->where(function($query) {
                    $i = 0;
                    foreach ($this->getOption('columns') as $column) {
                        if (isset($column['search'])) {
                            if ($i == 0) {
                                $query->where($column['id'], 'like', '%' . $this->request->input('search.value') . '%');
                            } else {
                                $query->orWhere($column['id'], 'like', '%' . $this->request->input('search.value') . '%');
                            }
                        }
                        $i++;
                    }
                });

                $this->setOption('recordsFiltered', $model->count());
            } else {
                $this->setOption('recordsFiltered', $count);
            }

            if ($this->request->input('length') > 0) { // All = -1
                if ($this->request->input('start') > 0) {
                    $model = $model->skip($this->request->input('start'));
                }

                $model = $model->take($this->request->input('length'));
            }

            $this->setOption('data', $model->get()->toArray());
        } else {
            $this->setOption('count', $count);
            $this->setOption('ajax', $count > \Config::get('datatables.clientSideLimit'));

            if (!$this->getOption('ajax')) {
                $model = $model->select($columnsData['columns']);
                $model = $model->orderBy($columnsData['orderByColumn'], $this->getOption('order'));

                $data = $model->get()->toArray();

                if (count($columnsData['aggregates'])) {
                    foreach ($data as $key => $items) {
                        foreach ($columnsData['aggregates'] as $aggregate) {
                            $relation = snake_case($aggregate['aggregate']);
                            if (count($items[$relation])) {
                                $data[$key][$aggregate['id']] = $items[$relation][0]['aggregate'];
                            } else {
                                $data[$key][$aggregate['id']] = 0;
                            }

                            unset($data[$key][$relation]);
                        }
                    }
                }

                $this->setOption('data', $data);
            }
        }
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function createTable($table, $options)
    {
        $this->tables[$table] = $options;
    }

    public function getTables($table = null)
    {
        return $table ? $this->tables[$table] : $this->tables;
    }

    public function getOption($key)
    {
        return $this->tables[$this->getTable()][$key];
    }

    public function setOption($key, $value)
    {
        $this->tables[$this->getTable()][$key] = $value;
    }

    public function getColumnsData()
    {
        $columnsData = ['aggregates' => []];
        $columns = array_where($this->getOption('columns'), function ($key, $column) use (&$columnsData) {
            if (isset($column['aggregate'])) {
                array_push($columnsData['aggregates'], $column);
                return false;
            } else {
                return true;
            }
        });

        $columnsData['columns'] = array_column($columns, 'id');
        if ($this->getOption('checkbox')) {
            array_unshift($columnsData['columns'], 'id');
        }

        $columnsData['orderByColumn'] = $this->getOption('columns')[$this->getOption('orderByColumn')]['id'];

        return $columnsData;
    }
}
