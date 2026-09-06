<?php

namespace App\Http\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ErrorResponse extends Exception
{
    protected $type;

    protected $factor;

    public function __construct($message = 'Something is wrong', $code = 400, $type = 'info', $factor = [])
    {
        parent::__construct();
        $this->message = $message;
        $this->code = $code;
        $this->factor = $factor;
        $this->type = $type;
    }

    public function render(Request $request)
    {
        $errorOutput = [
            'error' => $this->getMessage(),
            'message' => $this->getMessage(),
            'factor' => $this->factor,
            'type' => $this->type,
        ];

        if ($request->ajax() && ! $request->is('api/*')) {
            return response()->json($errorOutput, $this->getCode());
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            return response($errorOutput, $this->getCode());
        }
        if ($request->is('api/*')) {
            return response()->json($errorOutput, $this->getCode());
        }

        return response()->json($errorOutput, $this->getCode());
    }

    public function getType(): string
    {
        return $this->type;
    }
}
