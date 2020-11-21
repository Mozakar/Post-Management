<?php
namespace App\Services\Api\V1\Auth;


interface IAuthService
{

    /**
     * Login Or Register function
     *
     * @param Request $request
     * @return array
     */
    public  function loginOrRegister($request);

    /**
     * Veify function
     *
     * @param Request $request
     * @return array
     */
    public  function verify($request);

    /**
     * logout function
     *
     * @param Request $request
     * @return array
     */
    public  function logout($request);

}
