<?php
/**
 * Error.php => Catch and show Yaf Error
 */

class ErrorController extends Yaf_Controller_Abstract {

    /** 
     * Show YAF Error 
     * only display errors under DEV, else save error in LOG_FILE
     * @param exception $exception
     * @return void
     */
    public function errorAction($exception){
        switch ($exception->getCode()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                if(ENV == 'DEV'){
                    echo 404, ":", $exception->getMessage();
                }else{
                    echo 404;
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }    
            break;

            default :
                if(ENV == 'DEV'){
                    echo 0, ":", $exception->getMessage();
                }else{
                    echo 'Unknown error';
                    file_put_contents(LOG_FILE, $exception->getMessage().PHP_EOL, FILE_APPEND);
                }      
            break; 
        }
    }
}