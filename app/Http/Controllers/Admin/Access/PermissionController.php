<?php

namespace App\Http\Controllers\Admin\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index(){
    $permissions = Permission::paginate(200);
    $seo = [
        'title' => 'Разрешения'
    ];
    return view('template.admin.access.permission.index', compact('permissions', 'seo'));
  }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $roles = Role::all();
      $seo = [
          'title' => 'Добавить разрешение'
      ];
      return view('template.admin.access.permission.create', compact('roles','seo'));
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
      $permission = Permission::create([
          'name' => $request->name
      ]);
      if ($request->roles){
        $roles = Role::whereIn('id', $request->roles)->get();
        $permission->syncRoles($roles);
      }
      return redirect()->route('admin.permissions.index')->with([
          'success' => 'Новое разрашение успешно добавлено'
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
      $permission = Permission::findById($id);
      $roles = Role::all();
      $seo = [
          'title' => 'Обновить разрешение'
      ];
      return view('template.admin.access.permission.edit', compact('seo', 'roles', 'permission'));
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
      $permission = Permission::findById($id);
      $permission->update([
          'name' => $request->name
      ]);
      if ($request->roles){
        $roles = Role::whereIn('id', $request->roles)->get();
        $permission->syncRoles($roles);
      }
      return redirect()->route('admin.permissions.index')->with([
          'success' => 'Разрешение успешно обновлено'
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
      $permission = Permission::findById($id);
      $permission->delete();
      return redirect()->route('admin.permissions.index')->with([
          'success' => 'Разрешение успешно удалено'
      ]);
    }
}
