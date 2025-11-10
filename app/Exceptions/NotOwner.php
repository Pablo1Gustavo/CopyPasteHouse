<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotOwner extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'The user is not the owner of this resource.'
        ], Response::HTTP_FORBIDDEN);
    }
}
