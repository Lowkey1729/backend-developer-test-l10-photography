<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ViewModels\AchievementViewModel;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json(
            new AchievementViewModel($user)
        );
    }
}
