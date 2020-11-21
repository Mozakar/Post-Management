<?php
if (!function_exists('responseGenerator')) {
    function responseGenerator()
    {
        /** @var ResponseGenerator $responseGenerator */
        $responseGenerator = resolve('App\Libs\ResponseGenerator');
        return $responseGenerator;
    }
}


if (!function_exists('logActivity')) {
    function logActivity()
    {
        /** @var SystemLog $logActivity */
        $logActivity = resolve('App\Libs\SystemLog');
        return $logActivity;
    }
}



if (!function_exists('helper')) {
    function helper()
    {
        /** @var helper $helper */
        $helper = resolve('App\Libs\Helper');
        return $helper;
    }
}

if (!function_exists('storeImage')) {
    function storeImage()
    {
        /** @var StoreImage $storeImage */
        $storeImage = resolve('App\Libs\StoreImage');
        return $storeImage;
    }
}
