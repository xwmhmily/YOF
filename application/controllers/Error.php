<?php
/*
 * Error.php => Catch and show Yaf Error
 */

class ErrorController extends Yaf_Controller_Abstract {

	public function errorAction($exception){
        if(ENVIRONMENT == 'DEV'){ // only display errors under DEV
            switch ($exception->getCode()) {
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_NOTFOUND_VIEW:
                    echo 404, ":", $exception->getMessage();
                break;

                default :
                    $message = $exception->getMessage();
                    echo 0, ":", $exception->getMessage();
                break;
            }
        }
	}

}