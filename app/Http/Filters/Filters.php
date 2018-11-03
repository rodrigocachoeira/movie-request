<?php

namespace App\Http\Filters;

use App\Usefuls\StringTrait;
use Illuminate\Http\Request;

/**
 * Class Filters
 *
 * Classe de definição de alguns
 * métodos que serão úteis no controle
 * de filtros de entidades
 *
 * @package App\Core\Business\Filters
 * @author Laracasts
 * @version 1.0
 */
abstract class Filters
{

    use StringTrait;

    const MYSQL_LIKE = 'LIKE';

    const PGSQL_LIKE = 'iLIKE';

    /**
     * @var object
     */
    protected $request, $builder;

    /**
     * @var array
     */
    protected $filters, $orders, $default, $fixedFilters, $dependencies = [];

    /**
     * @param array $dependencies
     *
     * @return $this
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    /**
     * ThreadsFilters constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Verifica qual o banco de dados
     * que está sendo utilizado
     * e configura o element LIKE
     * de acordo com ele
     *
     * @return string
     */
    public function likePattern ()
    {
        if (env('DB_CONNECTION') == 'public') {
            return self::PGSQL_LIKE;
        }
        return self::MYSQL_LIKE;
    }

    /**
     * Realiza as chamadas dos métodos de
     * filtro da entidade
     *
     * @param $builder
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value){
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }else {
                $this->genericLike($filter, $value);
            }
        }
        if (count($this->dependencies) > 0)
            $this->withDependences();

        foreach ($this->default ?? [] as $default) {
            if (method_exists($this, $default)) {
                $this->$default();
            }
        }

        foreach ($this->orders ?? [] as $column => $direction) {
            $this->builder->orderBy($column, $direction);
        }

        foreach ($this->fixedFilters ?? [] as $filter) {
            if (method_exists($this, $filter)) {
                $this->$filter();
            }
        }

        return $this->builder;
    }

    /**
     * Quando a funcao nao é encontrada aplica-se
     * o método like
     *
     * @param $value
     */
    private function genericLike ($column, $value)
    {
        $this->builder->where($this->uppercaseToUnderline($column), $this->likePattern(), '%'.$value.'%');
    }

    /**
     * Realiza uma consulta de registros
     * juntamente com sua dependências
     *
     * @return mixed
     */
    public function withDependences ()
    {
        foreach ($this->dependencies as $dependency) {
            $this->builder->with($dependency);
        }
        return $this->builder;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return collect($this->request->all())->intersectByKeys(collect($this->filters)->flip());
    }

    /**
     * Retorna o schema informado como 
     * parâmetro, se e somente se a aplicação
     * estiver com o banco de dados PGSQL
     * 
     * @param string $schema
     * @return string
     */
    public function getSchema (string $schema)
    {
        if (config('app.env') === 'testing') {
            return '';
        }
        return $schema.'.';
    }

}