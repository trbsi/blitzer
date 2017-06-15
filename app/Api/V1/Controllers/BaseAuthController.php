<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class BaseAuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->authUser = $this->user->getAuthenticatedUser();
    }
}
