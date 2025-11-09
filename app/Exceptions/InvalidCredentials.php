<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidCredentials extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'Invalid login credentials provided.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
