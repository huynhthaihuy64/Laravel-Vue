<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Users\SendMailAllRequest;
use App\Http\Services\Mail\SendMailAllUserService;

class SendMailAllUserController extends BaseController
{
    protected $sendMailAllUserService;

    public function __construct(SendMailAllUserService $sendMailAllUserService)
    {
        $this->sendMailAllUserService = $sendMailAllUserService;
    }

    /**
     * @param SendMailAllRequest $request
     *
     */
    public function sendAll(SendMailAllRequest $request)
    {
        return $this->sendMailAllUserService->sendAll($request->only('subject','content'));
    }
}
