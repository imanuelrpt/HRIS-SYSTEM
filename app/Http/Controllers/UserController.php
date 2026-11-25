<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $roleFilter = $request->string('role')->toString();
        $statusFilter = $request->string('status')->toString();

        $users = User::query()
            ->with([
                'department:id,name',
                'position:id,name',
                'manager:id,name',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when(in_array($roleFilter, array_column(Role::cases(), 'value'), true), fn ($query) => $query->where('role', $roleFilter))
            ->when($statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($statusFilter === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', array_merge([
            'user' => new User,
            'isEditing' => false,
        ], $this->formOptions()));
    }

    public function store(StoreUserRequest $request, UserManagementService $userManagement): RedirectResponse
    {
        $user = $userManagement->create($request->validated());

        return redirect()->route('users.index')->with('success', "Karyawan {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('users.form', array_merge([
            'user' => $user,
            'isEditing' => true,
        ], $this->formOptions()));
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserManagementService $userManagement,
    ): RedirectResponse {
        $userManagement->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', "Data karyawan {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user, UserManagementService $userManagement): RedirectResponse
    {
        $userManagement->delete($user);

        return redirect()->route('users.index')->with('success', "Karyawan {$user->name} berhasil dinonaktifkan.");
    }

    /**
     * @return array{
     *     departments: Collection<int, string>,
     *     positions: Collection<int, string>,
     *     managers: Collection<int, string>,
     *     roleOptions: Collection<string, string>
     * }
     */
    private function formOptions(): array
    {
        return [
            'departments' => Department::query()->orderBy('name')->pluck('name', 'id'),
            'positions' => Position::query()
                ->with('department:id,name')
                ->get()
                ->mapWithKeys(fn (Position $position) => [
                    $position->id => "{$position->department->name} — {$position->name}",
                ]),
            'managers' => User::query()
                ->where('role', Role::Manager->value)
                ->orderBy('name')
                ->pluck('name', 'id'),
            'roleOptions' => $this->roleOptions(),
        ];
    }

    /**
     * @return Collection<string, string>
     */
    private function roleOptions(): Collection
    {
        return collect(Role::cases())->mapWithKeys(fn (Role $role) => [$role->value => $role->label()]);
    }
}
