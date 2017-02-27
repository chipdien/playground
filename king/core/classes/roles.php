<?php  if (!defined('ABSPATH')) exit('No direct script access allowed');

class Roles {
    private $id;
	private $name;
	private $permissions;
	private $maxPostsPerDay;
	private $maxFbAccounts;
    private $error;

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the value of permissions.
     *
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Sets the value of permissions.
     *
     * @param mixed $permissions the permissions
     *
     * @return self
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Gets the value of maxPostsPerDay.
     *
     * @return mixed
     */
    public function getMaxPostsPerDay()
    {
        return $this->maxPostsPerDay;
    }

    /**
     * Sets the value of maxPostsPerDay.
     *
     * @param mixed $maxPostsPerDay the max posts per day
     *
     * @return self
     */
    public function setMaxPostsPerDay($maxPostsPerDay)
    {
        $this->maxPostsPerDay = $maxPostsPerDay;
        return $this;
    }

        /**
     * Gets the value of error.
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets the value of error.
     *
     * @param mixed $error the error
     *
     * @return self
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of maxFbAccounts.
     *
     * @return mixed
     */
    public function getMaxFbAccounts()
    {
        return $this->maxFbAccounts;
    }

    /**
     * Sets the value of maxFbAccounts.
     *
     * @param mixed $maxFbAccounts the max fb accounts
     *
     * @return self
     */
    public function setMaxFbAccounts($maxFbAccounts)
    {
        $this->maxFbAccounts = $maxFbAccounts;
        return $this;
    }

    public function getRoles(){
        $roles = new ArrayObject(); 
        $dbReseult = DB::GetInstance()->QueryGet("SELECT * FROM roles");
        if($dbReseult->count()){
            foreach($dbReseult->results() as $r){
                $temp = new Roles();
                $temp->setId($r->id);
                $temp->setName($r->name);
                $temp->setPermissions($r->permissions);
                $temp->setMaxPostsPerDay($r->max_posts);
                $temp->setMaxFbAccounts($r->max_fbaccount);
                $roles[] = $temp;
            }
        }

        return $roles;
    }

    public function getRoleById($id){
        $dbReseult = DB::GetInstance()->QueryGet("SELECT * FROM roles WHERE id = ? ",array($id));
        if($dbReseult->count()){
            $r = $dbReseult->first();
            $this->setId($r->id);
            $this->setName($r->name);
            $this->setPermissions($r->permissions);
            $this->setMaxPostsPerDay($r->max_posts);
            $this->setMaxFbAccounts($r->max_fbaccount);
            return $this;
        }

        return false;
    }

    public function update(){

        // The user must be an admin to update roles
        $user = new User();

        if(!$user->hasPermission("admin")){
            $this->setError(lang("You don't have enough permissions to perform this operation"));
            return false;
        }

        $fields = array();

        if($this->permissions)      $fields['permissions']  = $this->permissions;
        if($this->maxPostsPerDay)   $fields['max_posts']    = $this->maxPostsPerDay;
        if($this->maxFbAccounts)    $fields['max_fbaccount']= $this->maxFbAccounts;

        if(count($fields)){
           try{
                 DB::GetInstance()->update("roles","id",$this->id,$fields);
                return true;
            }catch(Exception $e){
                $this->setError($e->getMessage());
                return false;
            }  
        }
        
    }

    public function add(){

        // The user must be an admin to update roles
        $user = new User();

        if(!$user->hasPermission("admin")){
            $this->setError(lang("You don't have enough permissions to perform this operation"));
            return false;
        }

        $fields = array();

        if($this->name)             $fields['name']  = $this->name;
        if($this->permissions)      $fields['permissions']  = json_encode($this->permissions);
        if($this->maxPostsPerDay)   $fields['max_posts']    = $this->maxPostsPerDay;
        if($this->maxFbAccounts)    $fields['max_fbaccount']= $this->maxFbAccounts;

        if(count($fields)){
           try{
                DB::GetInstance()->insert("roles",$fields);
                return true;
            }catch(Exception $e){
                $this->setError($e->getMessage());
                return false;
            }  
        }
        
    }

}
?>