<?php
namespace App\Services\Api\V1\Post;

use App\Http\Resources\PostCollection;
use App\Models\Post;


class PostService implements IPostService
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * find function
     *
     * @param Integer $id
     * @return model
     */
    public function find($id){
        return $this->post->find($id);
    }

    /**
     * query function
     *
     * @return model
     */
    public function query(){
        return $this->post;
    }


    public function show($id){
        $findPost = $this->post->find($id);
        return responseGenerator()->success(new PostCollection($findPost));
    }

    /**
     * get function
     *
     * @param Request $request
     * @return json
     */
    public  function get($request){


        $posts = $this->post->paginate(20);
        $response = [
            'posts' => PostCollection::collection($posts),
            'data'     => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'total'    => $posts->total()
            ]
        ];
        return  responseGenerator()->success($response);
    }



    /**
     * create function
     *
     * @param Request $request
     * @return json
     */
    public  function create($request, $data){
        $newPost = $this->post->create($data);
        $newPost->images()->detach();
        if (isset($data['image_id'])) {
            $newPost->images()->sync([$data['image_id']]);
        }
        return  responseGenerator()->success(new PostCollection($newPost));
    }


    /**
     * update function
     *
     * @param Request $request
     * @return json
     */
    public  function update($request, $data){
        $findPost = $this->post->find($data['id']);
        $findPost->update($data);
        $findPost->images()->detach();
        if (isset($data['image_id'])) {
            $findPost->images()->sync([$data['image_id']]);
        }
        $findPost = $findPost->fresh();
        return  responseGenerator()->success(new PostCollection($findPost));
    }


    /**
     * delete function
     *
     * @param Integer $id
     * @return json
     */
    public  function delete($id){
        $findPost = $this->post->find($id);
        $findPost->images()->detach();
        $findPost->delete();
        $response = ['message' => __('messages.successfully_deleted')];
        return  responseGenerator()->success($response);
    }


}
