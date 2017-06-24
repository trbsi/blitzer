<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class BaseAuthController extends Controller
{
    public $user;
    public $authUser;

    public function __construct(User $user)
    {
        $this->user = $user;
        if(!$this->authUser) {
            $this->authUser = $this->user->getAuthenticatedUser();
        }
    }
}
