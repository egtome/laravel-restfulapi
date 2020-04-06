<?php

namespace App\Traits;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    private function successResponse($data,$code)
    {
        return response()->json($data,$code);
    }
    
    protected function errorResponse($message,$code)
    {
        return response()->json(['error' => $message, 'code' => $code],$code);
    }
    
    protected function showAll(Collection $collection , $code = 200, $cache = 0)
    {
        if($collection->isEmpty()){
            return $this->successResponse(['data' => $collection],$code);
        }
        $transformer = $collection->first()->transformer;
        //Filter data
        $collection = $this->filterData($collection, $transformer);
        //Sort data
        $collection = $this->sortData($collection, $transformer);
        //Paginate data
        $collection = $this->paginateData($collection);
        //Transform data
        $collection = $this->transformData($collection, $transformer);
        //Cache data
        if($cache){
            $collection = $this->cacheData($collection,(int)$cache);
        }
        
        return $this->successResponse($collection,$code);
    }
    
    protected function showOne(Model $instance , $code = 200)
    {
        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);
        return $this->successResponse($instance,$code);
    }
    
    protected function showMessage($message , $code = 200)
    {
        return $this->successResponse(['data' => $message],$code);
    }
    
    protected function filterData(Collection $collection, $transformer)
    {
        foreach(request()->query() as $query => $value){
            $field = $transformer::originalAttribute($query);
            if(isset($field,$value)){
                $collection = $collection->where($field,$value);
            }
        }
        return $collection;
    }
    
    protected function sortData(Collection $collection, $transformer)
    {
        if(request()->has('sort_by')){
            $field = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy->{$field};
        }
        return $collection;
    }
    
    protected function paginateData(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];
        
        Validator::validate(request()->all(),$rules);
        
        $page = LengthAwarePaginator::resolveCurrentPage();
        //$page = request()->has('page') ? request()->page : 0;

        $perPage = 20;
        if(request()->has('per_page')){
            $perPage = (int)request()->per_page;
        }
        
        $results = $collection->slice(($page - 1) *- $perPage,$perPage)->values();
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginated->appends(request()->all());
        return $paginated;
    }
    
    protected function transformData($data, $transformer){
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }
    
    /*
     * Receives array, not collection (from transformer)
     * then cache data
     * sort query params to avoid cache issues 
     */
    protected function cacheData($data,$seconds){
        $url = request()->url();
        
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $url .= '?' . $queryString;
        //TTL (second parameter) in seconds after laravel 5.8
        return Cache::remember($url, $seconds, function() use($data){
            return $data;
        });
    }
}

