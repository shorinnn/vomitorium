<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Entrust\HasRole;

class User extends ConfideUser {
     public static $relationsData = array(
        'answers' => array(self::HAS_MANY, 'Block_answer'),
        'comments' => array(self::HAS_MANY, 'Answer_comment'),
        'remark' => array(self::HAS_MANY, 'Remark'),
      );
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');
        
        public function programs(){
            $now = date('Y-m-d H:i:s');
            $expired = "(`expires` IS NULL  OR `expires` > '$now')";
            $ids = DB::table('programs_users')->whereRaw($expired)->where('user_id',$this->id)->lists('program_id');
            if($ids=='' || count($ids)==0){
                return false;
                $program = Program::first();
                if($program==null) exit("BETA ERROR: THERE ARE NO PROGRAMS TO ASSOCIATE THIS USER WITH. Log in as the admin and create a program.");
                return array();
//                DB::table('programs_users')->insert(array('user_id'=>$this->id, 'program_id'=>$program->id));
//                $ids = array($program->id);
            }
            return DB::table('programs')->whereIn('id',$ids)->get();
        }
        
        public function program_values(){
            return implode(',',DB::table('programs_users')->where('user_id',$this->id)->lists('program_id'));
        }
        
        public function last_update(){
            if($this->last_update=='') return '0000-00-00 00:00:00';
            $last_update = json_decode($this->last_update, true);
            return $last_update[Session::get('program_id')];
        }
        
        public function update_admins($params){
            foreach($params['program'] as $program){
                if(!isset($params['assigned_admin'][$program]) || count($params['assigned_admin'][$program])==0){
                    DB::table('assigned_clients')->where('user_id', $this->id)->where('program_id', $program)->delete();
                }
                else{
                    // delete expired rows
                   // $ids = implode(',',$params['assigned_admin'][$program]);
                    DB::table('assigned_clients')->where('user_id', $this->id)->where('program_id', $program)->
                            whereNotIn('admin_id', $params['assigned_admin'][$program])->delete();
                    $current_admins =  DB::table('assigned_clients')->where('user_id', $this->id)->where('program_id', $program)->lists('admin_id');
                    foreach($params['assigned_admin'][$program] as $admin){
                        if(!in_array($admin, $current_admins)){
                            $data = array(
                                'user_id' => $this->id,
                                'admin_id' => $admin,
                                'program_id' => $program,
                            );
                            DB::table('assigned_clients')->insert($data);
                        }
                    }
                }
            }
            return true;
        }
        
        public function get_assignments(){
            return DB::table('assigned_clients')->where('user_id', $this->id)->get();
        }
        
        public function is_assigned($relations, $program_id, $admin_id){
            foreach($relations as $r){
                if($r->program_id == $program_id && $r->admin_id == $admin_id) return true;
            }
            return false;
        }
        
        public function coach($program_id){
            if(DB::table('assigned_clients')->where('user_id', $this->id)->where('program_id', $program_id)->count()==0){
                $admins = DB::table('assigned_roles')->where('role_id',1)->lists('user_id');
                return User::whereIn('id',$admins)->first();
            }
            return User::find(DB::table('assigned_clients')->where('user_id', $this->id)->where('program_id', $program_id)->first()->admin_id);
        }


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

        
        // use HasRole
        /**** PASTED HASROLE TRAIT HERE AS THERE IS NO SUPPORT IN PHP 5.3 ***/
    /*
     * Many-to-Many relations with Role
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust::role'), Config::get('entrust::assigned_roles_table'));
    }

    /**
     * Checks if the user has a Role by its name
     *
     * @param string $name Role name.
     *
     * @access public
     *
     * @return boolean
     */
    public function hasRole( $name )
    {
        foreach ($this->roles as $role) {
            if( $role->name == $name )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a permission by its name
     *
     * @param string $permission Permission string.
     *
     * @access public
     *
     * @return boolean
     */
    public function can( $permission )
    {
        foreach ($this->roles as $role) {
            // Deprecated permission value within the role table.
            if( is_array($role->permissions) && in_array($permission, $role->permissions) )
            {
                return true;
            }

            // Validate against the Permission table
            foreach($role->perms as $perm) {
                if($perm->name == $permission) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks role(s) and permission(s) and returns bool, array or both
     * @param string|array $roles Array of roles or comma separated string
     * @param string|array $permissions Array of permissions or comma separated string.
     * @param array $options validate_all (true|false) or return_type (boolean|array|both) Default: false | boolean
     * @return array|bool
     * @throws InvalidArgumentException
     */
    public function ability( $roles, $permissions, $options=array() ) {
        // Convert string to array if that's what is passed in.
        if(!is_array($roles)){
            $roles = explode(',', $roles);
        }
        if(!is_array($permissions)){
            $permissions = explode(',', $permissions);
        }

        // Set up default values and validate options.
        if(!isset($options['validate_all'])) {
            $options['validate_all'] = false;
        } else {
            if($options['validate_all'] != true && $options['validate_all'] != false) {
                throw new InvalidArgumentException();
            }
        }
        if(!isset($options['return_type'])) {
            $options['return_type'] = 'boolean';
        } else {
            if($options['return_type'] != 'boolean' &&
                $options['return_type'] != 'array' &&
                $options['return_type'] != 'both') {
                throw new InvalidArgumentException();
            }
        }

        // Loop through roles and permissions and check each.
        $checkedRoles = array();
        $checkedPermissions = array();
        foreach($roles as $role) {
            $checkedRoles[$role] = $this->hasRole($role);
        }
        foreach($permissions as $permission) {
            $checkedPermissions[$permission] = $this->can($permission);
        }

        // If validate all and there is a false in either
        // Check that if validate all, then there should not be any false.
        // Check that if not validate all, there must be at least one true.
        if(($options['validate_all'] && !(in_array(false,$checkedRoles) || in_array(false,$checkedPermissions))) ||
            (!$options['validate_all'] && (in_array(true,$checkedRoles) || in_array(true,$checkedPermissions)))) {
            $validateAll = true;
        } else {
            $validateAll = false;
        }

        // Return based on option
        if($options['return_type'] == 'boolean') {
            return $validateAll;
        } elseif($options['return_type'] == 'array') {
            return array('roles' => $checkedRoles, 'permissions' => $checkedPermissions);
        } else {
            return array($validateAll, array('roles' => $checkedRoles, 'permissions' => $checkedPermissions));
        }

    }

    /**
     * Alias to eloquent many-to-many relation's
     * attach() method
     *
     * @param mixed $role
     *
     * @access public
     *
     * @return void
     */
    public function attachRole( $role )
    {
        if( is_object($role))
            $role = $role->getKey();

        if( is_array($role))
            $role = $role['id'];

        $this->roles()->attach( $role );
    }

    /**
     * Alias to eloquent many-to-many relation's
     * detach() method
     *
     * @param mixed $role
     *
     * @access public
     *
     * @return void
     */
    public function detachRole( $role )
    {
        if( is_object($role))
            $role = $role->getKey();

        if( is_array($role))
            $role = $role['id'];

        $this->roles()->detach( $role );
    }

    /**
     * Attach multiple roles to a user
     *
     * @param $roles
     * @access public
     * @return void
     */
    public function attachRoles($roles)
    {
        foreach ($roles as $role)
        {
            $this->attachRole($role);
        }
    }

    /**
     * Detach multiple roles from a user
     *
     * @param $roles
     * @access public
     * @return void
     */
    public function detachRoles($roles)
    {
        foreach ($roles as $role)
        {
            $this->detachRole($role);
        }
    }
    
    public function beforeDelete() {
        //DB::table('block_answers')->where('user_id', $this->id)->delete();
        if($this->answers()->count() > 0){
             foreach($this->answers()->get() as $b){
                 $b->delete();
             }
         }
        DB::table('remarks')->where('user_id', $this->id)->delete();
    }

}
