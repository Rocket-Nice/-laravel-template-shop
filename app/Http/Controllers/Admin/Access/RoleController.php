<?php

namespace App\Http\Controllers\Admin\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $roles = Role::paginate(200);
      $seo = [
          'title' => 'Роли'
      ];
      return view('template.admin.access.role.index', compact('seo', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $permissions = Permission::all();
      $seo = [
          'title' => 'Добавить роль'
      ];
      return view('template.admin.access.role.create', compact('seo', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
          'name' => 'string|required'
      ]);
      $role = Role::create([
          'name' => $request->name
      ]);
      if ($request->permissions&&!empty($request->permissions)){
        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        $role->givePermissionTo($permissions);
      }

      return redirect()->route('admin.roles.index')->with([
          'success' => 'Новая роль успешно добавлена'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::all();
        $seo = [
            'title' => 'Обновить роль'
        ];
        return view('template.admin.access.role.edit', compact('seo','role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|required'
        ]);
        $role = Role::findById($id);
        $role->update([
            'name' => $request->name
        ]);
        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();

        $role->syncPermissions($permissions);
        return redirect()->route('admin.roles.index')->with([
            'success' => 'Роль "'.$role->name.'" успешно изменена'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $role = Role::findById($id);
      $role->delete();
      return redirect()->route('admin.roles.index')->with([
          'success' => 'Роль успешно удалена'
      ]);
    }
}
