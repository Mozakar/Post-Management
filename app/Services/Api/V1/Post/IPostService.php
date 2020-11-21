<?php
namespace App\Services\Api\V1\Post;


interface IPostService
{

    /**
     * find function
     *
     * @param Integer $id
     * @return model
     */
    public function find($id);

     /**
     * show function
     *
     * @param Integer $id
     * @return model
     */
    public function show($id);

    /**
     * query function
     *
     * @return model
     */
    public function query();

    /**
     * get function
     *
     * @param Request $request
     * @return json
     */
    public  function get($request);

    /**
     * create function
     *
     * @param Request $request
     * @return json
     */
    public  function create($request, $data);


    /**
     * update function
     *
     * @param Request $request
     * @return json
     */
    public  function update($request, $data);


    /**
     * delete function
     *
     * @param Integer $id
     * @return json
     */
    public  function delete($id);

}
