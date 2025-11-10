<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class WrongPastePassword extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'The provided password is incorrect.'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
