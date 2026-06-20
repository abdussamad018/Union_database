<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with(['house.village'])->latest()->paginate(20),
            'roles' => array_map(
                fn ($r) => ['value' => $r->value, 'label' => $r->label(app()->getLocale())],
                UserRole::cases()
            ),
            'houses' => House::with('village')->orderBy('house_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        DB::transaction(function () use ($data) {
            $user = User::create($data);
            $this->syncHouseRepresentative($user);
        });

        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::transaction(function () use ($user, $data) {
            $oldHouseId = $user->house_id;
            $user->update($data);
            $this->syncHouseRepresentative($user, $oldHouseId);
        });

        return back()->with('success', 'User updated.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'User updated.');
    }

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $isBariRep = $request->role === UserRole::BariRepresentative->value;

        $rules = [
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'locale' => 'nullable|in:bn,en',
            'is_active' => 'sometimes|boolean',
            'house_id' => [
                Rule::requiredIf($isBariRep),
                'nullable',
                'exists:houses,id',
            ],
            'phone' => [
                Rule::requiredIf($isBariRep),
                'nullable',
                'string',
                'max:20',
            ],
        ];

        if ($user) {
            $rules['password'] = 'nullable|min:8';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:8';
        }

        $data = $request->validate($rules);
        $data['locale'] = $data['locale'] ?? 'bn';

        if (! $isBariRep) {
            $data['house_id'] = null;
        }

        if ($isBariRep && ! empty($data['house_id'])) {
            $this->ensureHouseAvailable($data['house_id'], $user);
        }

        return $data;
    }

    protected function ensureHouseAvailable(int $houseId, ?User $user = null): void
    {
        $takenByUser = User::where('house_id', $houseId)
            ->where('role', UserRole::BariRepresentative)
            ->when($user, fn ($q) => $q->where('id', '!=', $user->id))
            ->exists();

        $house = House::find($houseId);
        $takenOnHouse = $house
            && $house->representative_user_id
            && (! $user || $house->representative_user_id !== $user->id);

        if ($takenByUser || $takenOnHouse) {
            throw ValidationException::withMessages([
                'house_id' => app()->getLocale() === 'bn'
                    ? 'এই বাড়ির ইতিমধ্যে একজন প্রতিনিধি আছে।'
                    : 'This house already has a representative.',
            ]);
        }
    }

    protected function syncHouseRepresentative(User $user, ?int $oldHouseId = null): void
    {
        if ($oldHouseId && $oldHouseId !== $user->house_id) {
            House::where('id', $oldHouseId)
                ->where('representative_user_id', $user->id)
                ->update(['representative_user_id' => null]);
        }

        if ($user->role !== UserRole::BariRepresentative || ! $user->house_id) {
            House::where('representative_user_id', $user->id)
                ->update(['representative_user_id' => null]);

            return;
        }

        House::where('representative_user_id', $user->id)
            ->where('id', '!=', $user->house_id)
            ->update(['representative_user_id' => null]);

        House::where('id', $user->house_id)->update(['representative_user_id' => $user->id]);
    }
}
