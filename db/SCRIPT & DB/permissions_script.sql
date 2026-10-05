insert into role_has_permissions (permission_id, role_id)
select permission_id, role_id from permission_role
;

insert into model_has_roles (role_id, model_type, model_id)
select role_id, 'App\\Models\\User', user_id from role_user
;

update roles, numbers set guard_name = 'web'
;





$query= DB::SELECT("SELECT a.id as user_id,b.role_id from users a JOIN model_has_roles b ON a.id = b.model_id and a.valid=1");

foreach ($query as $keys ) {
    DB::table('model_has_roles')->where('model_id', $keys->user_id)->delete();
    $userdata = User::query()->findOrFail($keys->user_id);
    $userdata->assignRole($keys->role_id);
}