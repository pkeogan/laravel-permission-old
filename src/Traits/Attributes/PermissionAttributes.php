<?php

namespace Pkeogan\Permission\Traits\Attributes;

use Pkeogan\Permission\Guard;
use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Permission;

trait PermissionAttributes
{
    /**
     *  Get this parents till it is null, then return the collection, if none, return null
     */
    public function getParentsAttribute()
    {
       if($this->parent == null){return null;}
        
        $parent = $this->parent;
        $permissions[] = $parent->id;
        
        while($parent->parent != null){
             $parent = $parent->parent; 
             $permissions[] = $parent->id;
        }
        
      return $permissions;
    }
     
    /**
     * Get the permisisons IDs and then Parents IDs, and then return them as a array of IDs.
     */
    public function getIdWithParentsAttribute()
    {
        if(! is_null($this->parent)){
            return array_merge($this->parents, [$this->id]);           
        } 
        return $permissions = array($this->id);
    }
    
  
}
