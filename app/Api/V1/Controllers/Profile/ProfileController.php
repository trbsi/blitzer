<?php

namespace App\Api\V1\Controllers\Profile;

use App\Api\V1\Controllers\BaseAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\SendPushNotification;
use DB;

class ProfileController extends BaseAuthController
{
	/**
	 * Favorite spcific user
	 * @param  Request $request
	 */
	public function favoriteUser(Request $request)
	{
		DB::beginTransaction();
		try {
			$this->authUser->favoriteUser()->attach($request->user_id);
			DB::commit();			
			//user who was favorited
			$user2 = User::find($request->user_id);
			//send notification to another user that he was favorited
			$dataTmp =
            [
                'title' => trans('user.profile.somebody_likes_you'), //user who sent message (ME)
                'body' => trans('user.profile.you_were_favorited', [
                	'user1' => $user2->first_name." ".$user2->last_name, 
                	'user2' => $this->authUser->first_name." ".$this->authUser->last_name,
                	'him_her' => ($this->authUser->gender == 'male' ? 'him' : 'her')]),
                'sound' => "default",
            ];
			SendPushNotification::sendNotification($user2->id, $dataTmp);
			$response = ['status' => 'favorited'];
		} catch (\Exception $e) {
			DB::rollBack();
			$this->authUser->favoriteUser()->detach($request->user_id);
			$response = ['status' => 'unfavorited'];
		}

        return response()->json($response);
	}
}