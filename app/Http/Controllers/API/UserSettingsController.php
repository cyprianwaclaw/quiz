<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreUserSettingsRequest;
use App\Http\Requests\UpdateUserSettingsRequest;
use App\Http\Requests\UploadUserAvatarRequest;
use App\Http\Resources\API\UserSettingsResource;
use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Registration
 * @group Operation about user
 */

class UserSettingsController extends APIController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserSettingsRequest $request)
    {
        //
    }

    /**
     * Display user settings.
     * @group Settings
     * @responseFile 200 scenario="Success" storage/api-docs/responses/users/settings/show.200.json
     *
     * @return array
     */
    public function show(): array
    {
        /** @var User $user */
        $user = User::with(['company', 'financial'])->where('id', auth()->id())->first();
        $result['personal'] = [
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        $company = $user->company;
        if($company) {
            $result['company'] = [
                'name' => $company->name,
                'nip' => $company->nip,
                'regon' => $company->regon,
                'address' => $company->address,
            ];
        }else $result['company'] = null;

        $financial = $user->financial;
        if($financial) {
            $result['financial'] = [
                'iban' => $financial->iban,
                'bank_name' => $financial->bank_name,
                'swift' => $financial->swift,
            ];
        }else $result['financial'] = null;
        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserSettings  $userSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(UserSettings $userSettings)
    {
        //
    }

    /**
     * Update user settings.
     * @group Settings
     *
     * @responseFile 200 scenario="Success" storage/api-docs/responses/users/settings/update.200.json
     *
     * @param  \App\Http\Requests\UpdateUserSettingsRequest  $request
     * @param  \App\Models\UserSettings  $userSettings
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserSettingsRequest $request)
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = User::with(['company', 'financial', 'company.address'])->where('id', auth()->id())->first();
        $user->fill($request->safe()->only('name', 'surname', 'email', 'phone'));
        $user->save();

        if ($request->safe(['company_name'])) {
            $company_request = $request->safe()->only(['company_name', 'nip', 'regon']);
            $company_request['name'] = $company_request['company_name'];
            unset($company_request['company_name']);
            $user->company()->updateOrCreate([], $company_request);
            $address_request = $request->safe()->only(['city', 'postcode', 'street', 'building_number', 'house_number']);
            $user->refresh();
            $user->company->address()->updateOrCreate([], $address_request);
        }

        if ($request->safe(['bank_name'])) {
            $financial_request = $request->safe()->only(['bank_name', 'iban', 'swift']);
            $user->financial()->updateOrCreate([], $financial_request);
        }
        return $this->sendResponse(new UserSettingsResource($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserSettings  $userSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserSettings $userSettings)
    {
        //
    }


    /**
     * Upload user avatar
     *
     * @bodyParam avatar file required Must be an image.
     *
     * @responseFile status=200 scenario="Avatar updated" storage/api-docs/responses/users/uploadAvatar.200.json
     * @responseFile status=422 scenario="Avatar no updated" storage/api-docs/responses/users/uploadAvatar.422.json
     */
    public function uploadUserPhoto(UploadUserAvatarRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        // check if image has been received from form
        $validated = $request->validated();

        // check if user has an existing avatar
        if($user->avatar_path != NULL){
            // delete existing image file
            Storage::disk('user_avatars')->delete($user->avatar_path);
        }
       x // processing the uploaded image
        $avatar_path = $validated['avatar']->store('','user_avatars');

        // Update user's avatar column on 'users' table
        $user->avatar_path = $avatar_path;

        if($user->save()){
            return response()->json([
                'status'    =>  'success',
                'message'   =>  'User avatar updated!',
                'avatar_url'=>  url('storage/user-avatar/'.$avatar_path)
            ]);
        }else{
            return response()->json([
                'status'    => 'failure',
                'message'   => 'Failed to update user avatar!',
                'avatar_url'=> NULL
            ]);
        }
    }
}