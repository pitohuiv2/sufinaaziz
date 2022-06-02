<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //        $this->reportable(function (Throwable $e) {
        //            //
        //        });
        $this->renderable(function (\Exception $e, $request) {
            return $this->handleException($request, $e);
        });
    }
    public function handleException($request, \Exception $exception)
    {
        try {
            \DB::connection()->getPdo();
            if (\DB::connection()->getDatabaseName()) {
            } else {
                $msg = 'Sorry DB Not Found';
                return response()->view('handler.db-handler', compact('msg'));
            }
        } catch (\Exception $e) {
            $msg = 'Sorry, ' . $e->getMessage();
            return response()->view('handler.db-handler', compact('msg'));
        }
        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        } elseif ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        } elseif ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof AuthorizationException) {
            $exception = new HttpException(403, $exception->getMessage());
        } elseif ($exception instanceof ValidationException&& $exception->getResponse()) {
            return $exception->getResponse();
        }
    
        if ($this->isHttpException($exception)) {
            switch ($exception->getStatusCode()) {

                // not authorized
                case '403':
                    $res = array(
                        'message' => 'Not Authorized',
                        'error' => $exception->getMessage() . ' Line ' . $exception->getLine(),
                        'as' => 'Xoxo',
                    );
                    return response()->json($res, 403);
                    break;
    
                // not found
                case '404':
                    $res = array(
                        'message' => 'Not Found',
                        'error' => $exception->getMessage() . ' Line ' . $exception->getLine(),
                        'as' => 'Xoxo',
                    );
                    return response()->json($res, 404);
                
                    break;
    
                // internal error
                case '500':
                    $res = array(
                        'message' => 'Internal Error',
                        'error' => $exception->getMessage() . ' Line ' . $exception->getLine(),
                        'as' => 'Xoxo',
                    );
                    return response()->json($res, 404);
                    break;
    
                default:
                    return $this->renderHttpException($exception);
                    break;
            }
        }
        if ($exception instanceof Throwable) {

            $res = array(
                'message' => 'Internal Error',
                'error' => $exception->getMessage() . ' Line ' . $exception->getLine(),
                'as' => 'Xoxo',
            );
            return response()->json($res, 500);
        }
    }
}
