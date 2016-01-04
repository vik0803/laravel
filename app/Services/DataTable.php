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

        foreach ($columnsData['joins'] as $join) {
            array_push($columnsData['columns'], $join['selector']);
            $model = $model->leftJoin($join['join']['table'], $join['join']['localColumn'], $join['join']['constrain'], $join['join']['foreignColumn']);
        }

        foreach ($columnsData['appends'] as $append) {
            array_push($columnsData['columns'], $append['append']['selector']);
        }

        foreach ($columnsData['prepends'] as $prepend) {
            array_push($columnsData['columns'], $prepend['prepend']['selector']);
        }

        foreach ($columnsData['links'] as $link) {
            array_push($columnsData['columns'], $link['link']['selector']);
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
                                $query->where($column['selector'], 'like', '%' . $this->request->input('search.value') . '%');
                            } else {
                                $query->orWhere($column['selector'], 'like', '%' . $this->request->input('search.value') . '%');
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

            $data = $model->get()->toArray();

            if (count($columnsData['aggregates'])) {
                $data = $this->aggregate($data, $columnsData['aggregates']);
            }

            if (count($columnsData['appends'])) {
                $data = $this->append($data, $columnsData['appends']);
            }

            if (count($columnsData['prepends'])) {
                $data = $this->prepend($data, $columnsData['prepends']);
            }

            if (count($columnsData['links'])) {
                $data = $this->link($data, $columnsData['links']);
            }

            $this->setOption('data', $data);
        } else {
            $this->setOption('count', $count);
            $this->setOption('ajax', $count > \Config::get('datatables.clientSideLimit'));

            if (!$this->getOption('ajax')) {
                $model = $model->select($columnsData['columns']);
                $model = $model->orderBy($columnsData['orderByColumn'], $this->getOption('order'));

                $data = $model->get()->toArray();

                if (count($columnsData['aggregates'])) {
                    $data = $this->aggregate($data, $columnsData['aggregates']);
                }

                if (count($columnsData['appends'])) {
                    $data = $this->append($data, $columnsData['appends']);
                }

                if (count($columnsData['prepends'])) {
                    $data = $this->prepend($data, $columnsData['prepends']);
                }

                if (count($columnsData['links'])) {
                    $data = $this->link($data, $columnsData['links']);
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
        $columnsData = ['prepends' => [], 'appends' => [], 'links' => [], 'joins' => [], 'aggregates' => []];
        $columns = array_where($this->getOption('columns'), function ($key, $column) use (&$columnsData) {
            $skip = false;

            if (isset($column['join'])) {
                array_push($columnsData['joins'], $column);
                $skip = true;
            }

            if (isset($column['aggregate'])) {
                array_push($columnsData['aggregates'], $column);
                $skip = true;
            }

            if (isset($column['append'])) {
                array_push($columnsData['appends'], $column);
            }

            if (isset($column['prepend'])) {
                array_push($columnsData['prepends'], $column);
            }

            if (isset($column['link'])) {
                array_push($columnsData['links'], $column);
            }

            if ($skip) {
                return false;
            } else {
                return true;
            }
        });

        $columnsData['columns'] = array_column($columns, 'selector');
        if ($this->getOption('checkbox')) {
            array_unshift($columnsData['columns'], $this->getOption('checkbox')['selector']);
        }

        $columnsData['orderByColumn'] = (is_numeric($this->getOption('orderByColumn')) ? $this->getOption('columns')[$this->getOption('orderByColumn')]['selector'] : $this->getOption('orderByColumn'));

        return $columnsData;
    }

    public function aggregate($data, $aggregates)
    {
        foreach ($data as $key => $items) {
            foreach ($aggregates as $aggregate) {
                $relation = snake_case($aggregate['aggregate']);
                if (count($items[$relation])) {
                    $data[$key][$aggregate['id']] = $items[$relation][0]['aggregate'];
                } else {
                    $data[$key][$aggregate['id']] = 0;
                }

                unset($data[$key][$relation]);
            }
        }

        return $data;
    }

    public function append($data, $appends)
    {
        foreach ($data as $key => $items) {
            foreach ($appends as $append) {
                if (array_key_exists($append['id'], $items)) {
                    $passed = false;
                    foreach ($append['append']['rules'] as $column => $value) {
                        if ($items[$column] == $value) {
                            $passed = true;
                        } else {
                            $passed = false;
                        }
                    }

                    if ($passed) {
                        $data[$key][$append['id']] .= $append['append']['text'];
                    }
                }
            }
        }

        return $data;
    }

    public function prepend($data, $prepends)
    {
        foreach ($data as $key => $items) {
            foreach ($prepends as $prepend) {
                if (array_key_exists($prepend['id'], $items)) {
                    $passed = false;
                    foreach ($prepend['prepend']['rules'] as $column => $value) {
                        if ($items[$column] == $value) {
                            $passed = true;
                        } else {
                            $passed = false;
                        }
                    }

                    if ($passed) {
                        $data[$key][$prepend['id']] = $prepend['prepend']['text'] . $data[$key][$prepend['id']];
                    }
                }
            }
        }

        return $data;
    }

    public function link($data, $links)
    {
        foreach ($data as $key => $items) {
            foreach ($links as $link) {
                if (array_key_exists($link['id'], $items)) {
                    $passed = false;
                    foreach ($link['link']['rules'] as $column => $value) {
                        if ($items[$column] == $value) {
                            $passed = true;
                        } else {
                            $passed = false;
                        }
                    }

                    if ($passed) {
                        $data[$key][$link['id']] = '<a href="' . \Locales::route($link['link']['route'], (isset($link['link']['routeParameter']) ? $data[$key][$link['link']['routeParameter']] : '')) . '">' . (isset($link['link']['icon']) ? '<span class="glyphicon glyphicon-' . $link['link']['icon'] . ' glyphicon-left"></span>' : '') . $data[$key][$link['id']] . '</a>';
                    }
                }
            }
        }

        return $data;
    }
}
