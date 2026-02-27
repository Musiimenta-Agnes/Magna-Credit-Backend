<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Called by Flutter ProfilePage on load (_loadProfile)
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'profile' => $user->profile ? [
                'bio'             => $user->profile->bio ?? '',
                'address'         => $user->profile->address ?? '',
                'other_contact'   => $user->profile->other_contact ?? '',
                'kin_name'        => $user->profile->kin_name ?? '',
                'kin_contact'     => $user->profile->kin_contact ?? '',
                'income'          => $user->profile->income ?? '',
                'current_address' => $user->profile->current_address ?? '',
                'gender'          => $user->profile->gender ?? 'Other',
                'occupation'      => $user->profile->occupation ?? 'Other',
                'loan_type'       => $user->profile->loan_type ?? '',
                'education'       => $user->profile->education ?? '',
                'profile_image'   => $user->profile->profile_image
                    ? Storage::url($user->profile->profile_image)
                    : null,
            ] : null,
        ]);
    }

    /**
     * PATCH /api/profile
     * Called by Flutter ProfilePage when user taps Save
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'            => 'sometimes|string|max:255',
            'phone'           => 'sometimes|nullable|string|max:20',
            'bio'             => 'sometimes|nullable|string|max:500',
            'address'         => 'sometimes|nullable|string|max:255',
            'other_contact'   => 'sometimes|nullable|string|max:20',
            'kin_name'        => 'sometimes|nullable|string|max:255',
            'kin_contact'     => 'sometimes|nullable|string|max:20',
            'income'          => 'sometimes|nullable|string|max:50',
            'current_address' => 'sometimes|nullable|string|max:255',
            'gender'          => 'sometimes|nullable|string|max:20',
            'occupation'      => 'sometimes|nullable|string|max:100',
            'loan_type'       => 'sometimes|nullable|string|max:100',
            'education'       => 'sometimes|nullable|string|max:100',
            'profile_image'   => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        // Update name and phone on the users table
        $user->update($request->only(['name', 'phone']));

        // Handle profile image upload
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            // Delete old image to save storage space
            if ($user->profile?->profile_image) {
                Storage::disk('public')->delete($user->profile->profile_image);
            }
            $imagePath = $request->file('profile_image')
                ->store('profile_images', 'public');
        }

        // Gather all profile fields
        $profileData = $request->only([
            'bio', 'address', 'other_contact',
            'kin_name', 'kin_contact', 'income',
            'current_address', 'gender', 'occupation',
            'loan_type', 'education',
        ]);

        if ($imagePath) {
            $profileData['profile_image'] = $imagePath;
        }

        // Create the profile row if it doesn't exist, otherwise update it
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Return the updated profile so Flutter refreshes the page
        return $this->show($request);
    }

    /**
     * Called by LoanApplicationController after loan is submitted.
     * This is what makes loan form data appear on the profile page automatically.
     */
    public function syncFromLoanApplication($user, array $loanData): void
    {
        // Map: loan form field name => profile field name
        $map = [
            'full_name'      => 'name',           // → users.name
            'phone'          => 'phone',           // → users.phone
            'address'        => 'address',
            'other_contact'  => 'other_contact',
            'kin_name'       => 'kin_name',
            'kin_contact'    => 'kin_contact',
            'monthly_income' => 'income',
            'current_address'=> 'current_address',
            'gender'         => 'gender',
            'occupation'     => 'occupation',
            'loan_type'      => 'loan_type',
            'education'      => 'education',
        ];

        $userUpdate    = [];
        $profileUpdate = [];

        foreach ($map as $loanField => $profileField) {
            if (!isset($loanData[$loanField])) continue;
            if (in_array($profileField, ['name', 'phone'])) {
                $userUpdate[$profileField] = $loanData[$loanField];
            } else {
                $profileUpdate[$profileField] = $loanData[$loanField];
            }
        }

        if (!empty($userUpdate))    $user->update($userUpdate);
        if (!empty($profileUpdate)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileUpdate
            );
        }
    }
}