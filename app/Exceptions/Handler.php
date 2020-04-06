<?php

namespace App\Exceptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
class Handler extends ExceptionHandler
{
    use ApiResponser;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof ValidationException){
            $errors = $exception->validator->errors()->getMessages();
            
            if($this->isFrontend($request)){
                return $request->ajax() ? response()->json($errors,422) : redirect()->back()->withInput($request->input())->withErrors($errors);
            }
            return $this->errorResponse($errors,422);            
        }
        if($exception instanceof ModelNotFoundException){
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("$modelName not found", 404);
        }
        if($exception instanceof AuthenticationException){
            return $this->unauthenticated($request,$exception);
            //return $this->errorResponse('Unauthenticated', 401);
        }
        if($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(), 403);
        }
        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('Resource not found', 404);
        }
        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('Method not allowed', 405);
        }
        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if($exception instanceof QueryException){
            $errorCode = $exception->errorInfo[1];
            if($errorCode == 1451){
                return $this->errorResponse('This resourse is related with other resourse and cannot be removed', 409);
            }
        }
        if($exception instanceof TokenMismatchException){
            return redirect()->back()->withInput($request->input());
        }
        
        //At this point, unexpected exception
        
        if(env('APP_DEBUG')){
            //Show all details in debug mode only
            return parent::render($request, $exception);            
        }else{
            //Production, at this point, no idea what happened, a good idea is trigger an urgent notification to support when this happens
            return $this->errorResponse('Unexpected exception, please contact support', 500);
        }
    }
    
    protected function unauthenticated($request, AuthenticationException $exception): \Symfony\Component\HttpFoundation\Response {
        if($this->isFrontend($request)){
            return redirect()->guest('login');
        }
        return $this->errorResponse('Unauthenticated', 401);
    }
    
    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
