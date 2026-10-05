<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasTree
{
    protected function permissionTreeView($role = null)
    {
        $roleHasPermissions = null;

        if ($role !== null) {
            $roleHasPermissions = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->get()->toArray();
        }

        $permissions = DB::table('permissions')
            ->whereNull('permission_id')
            ->where('isActive', 1)
            ->get()->toArray();

        $tree = "<ul id='treeview' class='hummingbird-base'>";
            foreach ($permissions as $key => $permission) {
                $children = DB::table('permissions')
                    ->where('permission_id', $permission->id)
                    ->get()->toArray();

                $checked = '';

                if ($role !== null) {
                    if (isset($roleHasPermissions)) {
                        foreach ($roleHasPermissions as $hasPermission) {
                            $checked .= $permission->id === $hasPermission->permission_id ? 'checked' : '';
                        }
                    }
                }
//                dd(count($children));
                if (count($children)) {
                    $tree .= "<li style='margin-bottom:20px;'>
                                <i class='fa fa-minus'></i>
                                <label class='parent-menu'>
                                    <input id='{$permission->id}_{$key}' data-id='{$permission->id}_{$key}' {$checked} name='permission_id[]' value='{$permission->id}' type='checkbox' /> {$permission->name}
                                </label>"; // first layer

                    $tree .= $this->permissionChildView($children, $roleHasPermissions, $role);

                    $tree .= "<hr style='border-top: 1px solid #666'/>";
                } else {
                    $tree .= "<li style='margin-bottom:20px;'>
                                <label class='parent-menu'>
                                    <input id='{$permission->id}_{$key}' data-id='{$permission->id}_{$key}' {$checked} name='permission_id[]' value='{$permission->id}' type='checkbox' /> {$permission->name}
                                </label> <hr style='border-top: 1px solid #666'/>"; // first layer without count
                }
            }
        $tree .= "</ul>";

        return $tree;
    }

    protected function permissionChildView($children, $roleHasPermissions, $role): string
    {
        $loopLast = !next($children) ? null : '<hr/>';

        $tree = "<ul style='display: block'>";
            foreach ($children as $key => $child) {
                $childChildren = DB::table('permissions')
                    ->where('permission_id', $child->id)
                    ->get()->toArray();

                $checked = '';

                if ($role !== null) {
                    if (isset($roleHasPermissions)) {
                        foreach ($roleHasPermissions as $hasPermission) {
                            $checked .= $child->id === $hasPermission->permission_id ? 'checked' : '';
                        }
                    }
                }

                if (count($childChildren) === 0) {
                    $tree .= "<li style='margin-bottom:10px'>
                                <label>
                                    <input id='{$child->id}_{$key}' data-id='{$child->id}_{$key}' {$checked} name='permission_id[]' value='{$child->id}' type='checkbox' /> {$child->name}
                                </label>"; // last layer

                } else {
                    $tree .= "<li style='margin-bottom:20px;'>
                                <i class='fa fa-minus'></i>
                                <label class='parent-menu'>
                                    <input id='{$child->id}_{$key}' data-id='{$child->id}_{$key}' {$checked} name='permission_id[]' value='{$child->id}' type='checkbox' /> {$child->name}
                                </label>"; // last layer with count

                    $tree .= $this->permissionChildView($childChildren, $roleHasPermissions, $role);

                    $tree .= "</li> {$loopLast}";
                }
            }

        $tree .= "</ul>";

        return $tree;
    }

    protected function simpleTreeView()
    {
        $permissions = DB::table('permissions')
            ->whereNull('permission_id')
            ->where('isActive', 1)
            ->get()->toArray();

        $tree = '<ul class="my-tree">';
            foreach ($permissions as $permission) {
                $children = DB::table('permissions')
                    ->where('permission_id', $permission->id)
                    ->get()->toArray();

                $tree .= '<li><input type="checkbox" checked id="parent-'.$permission->id.'" />
                        <label class="my-tree_label" for="parent-'.$permission->id.'">
                            '.$permission->name.'
                        </label>';

                if (count($children)) {
                    $tree .= $this->simpleChildView($children);
                }
            }

        $tree .= '<ul>';

        return $tree;
    }

    protected function simpleChildView($children)
    {
        $html ='<ul>';
            foreach ($children as $child) {
                $childChildren = DB::table('permissions')
                    ->where('permission_id', $child->id)
                    ->get()->toArray();

                if (count($childChildren)) {
                    $html .= '<li>
                        <input type="checkbox" checked id="child-'.$child->id.'" />
                        <label for="child-'.$child->id.'" class="my-tree_label">
                            '.$child->name.'
                        </label>';

                    $html .= $this->simpleChildView($childChildren);
                } else {
                    $html .= '<li>
                        <span class="my-tree_label">
                            '.$child->name.'
                        </span>';

                    $html .= "</li>";
                }
            }

        $html .= "</ul>";

        return $html;
    }
}
