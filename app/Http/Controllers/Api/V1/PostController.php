<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Api\V1\Post\IPostService;

class PostController extends Controller
{
    private $postService;

    public function __construct(IPostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * get function
     *
     * @param Request $request
     * @return json
     */
    public  function get(Request $request){
        $response = $this->postService->get($request);
        return response()->json($response['data'], $response['status']);
    }

    /**
     * show  function
     *
     * @param Request $request
     * @return json
     */
    public  function show(Request $request, $id){
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $response = $this->postService->show($id);
        return response()->json($response['data'], $response['status']);
    }



    /**
     * create function
     *
     * @param Request $request
     * @return json
     */
    public  function create(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'image_id' => 'nullable',
            'image_id.*' => 'exists:files,id',
            'status' => 'required|in:publish,draft',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $data = $validator->validated();
        $response = $this->postService->create($request, $data);
        return response()->json($response['data'], $response['status']);
    }



    /**
     * update function
     *
     * @param Request $request
     * @return json
     */
    public  function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:posts,id',
            'title' => 'required',
            'content' => 'required',
            'image_id' => 'nullable',
            'image_id.*' => 'exists:files,id',
            'status' => 'required|in:publish,draft',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $data = $validator->validated();
        $response = $this->postService->update($request, $data);
        return response()->json($response['data'], $response['status']);
    }



    /**
     * update function
     *
     * @param Request $request
     * @return json
     */
    public  function updateStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:posts,id',
            'status' => 'required|in:publish,draft',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $data = $validator->validated();
        $response = $this->postService->update($request, $data);
        return response()->json($response['data'], $response['status']);
    }




    /**
     * delete function
     *
     * @param Request $request
     * @return json
     */
    public  function delete(Request $request, $id){
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $response = $this->postService->delete($id);
        return response()->json($response['data'], $response['status']);
    }



    public function getProducts(Request $request, $slug){
        $validator = Validator::make(['slug' => $slug], [
            'slug' => 'required|exists:categories,slug',
            'order_by' => 'sometimes|in:created_at,final_price',
            'order_type' => 'sometimes|in:asc,desc',
            'in_stock_products' => 'sometimes|in:all,true,false',
            'min_price' => 'sometimes',
            'max_price' => 'sometimes',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $findCategory = $this->postService->query()
                            ->where('slug', $slug)->first();

        $request['category_id'] = $findCategory->id;
        $response = $this->productService->get($request, true);
        return response()->json($response['data'], $response['status']);
    }

}
