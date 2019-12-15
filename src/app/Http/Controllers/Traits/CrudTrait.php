<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

/**
 * Description of CrudTrait
 *
 * @author ognestraz
 */
trait CrudTrait
{
    /**
     * Apply select
     *
     * @param Request $request
     * @param Model $query
     */
    protected function applySelect($request, $query)
    {
        $select = $request->get('select', null);
        if (null === $select) {
            return ;
        }

        $query->select(explode(',', $select));
    }

    /**
     * Apply limit and offset
     *
     * @param Request $request
     * @param Model $query
     */
    protected function applyLimit($request, $query)
    {
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $query->limit($limit);
        $query->offset($offset);
    }

    /**
     * Apply order
     *
     * @param Request $request
     * @param Model $query
     */
    protected function applyOrder($request, $query)
    {
        $sort = $request->get('sort', null);
        if (null === $sort) {
            return ;
        }

        $direction = 'asc';
        $field = $sort;
        if ('-' === $sort[0]) {
            $field = substr($sort, 1);
            $direction = 'desc';
        }

        $query->orderBy($field, $direction);
    }

    /**
     * Apply order
     *
     * @param Request $request
     * @param Model $query
     */
    protected function applyFilter($request, $query)
    {
        $filter = $request->get('filter', []);
        foreach ($filter as $field => $value) {
            $query->where($field, '=', $value);
        }
    }

    /**
     * Apply order
     *
     * @param Request $request
     * @param Model $query
     */
    protected function applyQuery($request, $query)
    {
        self::applySelect($request, $query);
        self::applyLimit($request, $query);
        self::applyOrder($request, $query);
        self::applyFilter($request, $query);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $modelClass = self::MODEL;
        $query = $modelClass::query();
        self::applyQuery($request, $query);
        return new JsonResponse($query->get(), JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $modelClass = self::MODEL;
        try {
            $model = $modelClass::create($request->all());
        } catch (QueryException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($model, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function read($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|between:1,100000000'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->getMessageBag(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $modelClass = self::MODEL;
        $model= $modelClass::findOrFail($id);
        return new JsonResponse($model, JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $modelClass = self::MODEL;
        $model= $modelClass::findOrFail($id);
        $model->fill($request->all());
        $model->save();
        return new JsonResponse($model, JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $modelClass = self::MODEL;
        $model= $modelClass::findOrFail($id);
        $model->delete();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
