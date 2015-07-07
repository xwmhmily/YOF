<?php
/*
 * Error.php => Catch and show Yaf Error
 */

class ErrorController extends Yaf_Controller_Abstract {

    // only display errors under DEV
    public function errorAction($exception){
        switch ($exception->getCode()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                if(ENV == 'DEV'){
                    echo 404, ":", $exception->getMessage();
                }else{
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }    
            break;

            default :
                if(ENV == 'DEV'){
                    echo 0, ":", $exception->getMessage();
                }else{
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }      
            break; 
        }
    }
}