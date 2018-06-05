<?php

namespace Pkeogan\Permission\Traits\Attributes;

use Pkeogan\Permission\Guard;
use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Permission;

trait HasPermissionAttributes
{
    /**
     *  Return all the gaurd names this model has
     *
     * @return Collection
     */
    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }
    
    /**
     *  Return the default gaurd name
     *
     * @return String
     */
    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }
    
    /**
     *  Create the Unique name of this model (Tablename+id) IE A ReportType model with and ID of 123 would be: report_type123_
     *
     * @return String
     */
    public function getUniqueNameAttribute(): string
    {
      return $this->getTable() . $this->id . '_';
    }
  
}
