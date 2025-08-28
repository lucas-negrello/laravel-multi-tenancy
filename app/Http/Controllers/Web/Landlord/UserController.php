<?php

namespace App\Http\Controllers\Web\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreUserRequest;
use App\Http\Requests\Landlord\UpdateUserRequest;
use App\Models\Landlord\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.users.index');
    }

    public function data(Request $request)
    {
        $query = User::verifiedTenantUser()->select(['id', 'name', 'email', 'created_at']);

        return DataTables::of($query)
            ->editColumn('created_at', function ($row) {
                return optional($row->created_at)->format('d-m-Y H:i:s');
            })
            ->toJson();
    }

    public function show(User $user): View
    {
        return view('pages.admin.users.show', compact('user'));
    }

    public function create(): View
    {
        return view('pages.admin.users.create');
    }

    public function store(StoreUserRequest $request): View
    {
        return view('pages.admin.users.store');
    }

    public function edit(User $user): View
    {
        return view('pages.admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): View
    {
        return view('pages.admin.users.update');
    }

    public function destroy(User $user): View
    {
        return view('pages.admin.users.destroy');
    }

}
