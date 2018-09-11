<?php

namespace Pkeogan\Permission\Traits\Collections;

use Illuminate\Support\Collection;

trait ExtendCollections {
	
	protected function registerCollectionBindings()
    {
    /**
     * Filter a Collection that have a relationship with ANY of the Collection Given.
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Collection
     */
      Collection::macro('filterIfHasAny', function ($collection2) {
          //Cycle through the given collection
          return $this->filter(function ($item, $key) use ($collection2) {
              //Check if current item has any of the given $collection2 IDs, if it does, then keep the item, if not, remove it
              if($item->{$collection2->first()->getTable()}->whereIn('id', $collection2->pluck('id')->toArray())->isNotEmpty()) //take the IDs from the $collection2 for performance
              {
                  //Item has at least 1 of the given collection2 items
                  return true;
              }
              else
              {
                  //Item has none of the given
                  return false;
              }
            });
        });
     /**
     * Filter a Collection that have a relationship with ALL of the Collection Given.
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Collection
     */
      Collection::macro('filterIfHasAll', function ($collection2) {
          //Cycle through the given collection
          return $this->filter(function ($item, $key) use ($collection2) {
              //Get the current items count of relationships that match the collection given, then compare that to the total of the given collection
              if($item->{$collection2->first()->getTable()}->whereIn('id', $collection2->pluck('id')->toArray())->count() ==  $collection2->count() ) //take the IDs from the $collection2 for performance
              {
                  //Item has at least 1 of the given collection2 items
                  return true;
              }
              else
              {
                  //Item has none of the given
                  return false;
              }
            });
        });
    /**
     * Filter a Collection that have a relationship with ONLY of the Collection Given.
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Collection
     */
      Collection::macro('filterIfHasOnly', function ($collection2) {
          //Cycle through the given collection
          return $this->filter(function ($item, $key) use ($collection2) {
              //Check if the item's relationsip count is the same as the given collection's count, it it isnt then reject
              // &&
              //Get the current items count of relationships that match the collection given, then compare that to the total of the given collection
              if($item->{$collection2->first()->getTable()}->count() == $collection2->count() && $item->{$collection2->first()->getTable()}->whereIn('id', $collection2->pluck('id')->toArray())->count() ==  $collection2->count() ) //take the IDs from the $collection2 for performance
              {
                  //Item has at least 1 of the given collection2 items
                  return true;
              }
              else
              {
                  //Item has none of the given
                  return false;
              }
            });
        });
		    /**
     * Filter a Collection of strings that start with a string
     *
     * @param String
     *
     * @return Collection
     */
      Collection::macro('filterIfStartsWith', function ($string) {
          //Cycle through the given collection
          return $this->filter(function ($item, $key) use ($string) {
                //Check if the string starts with the given string, if so keep it, if not reject it
				if(strpos($key, $string) === 0) //strpos is faster than substring checking
				{
					return true;
				}
				else
				{
					return false;
				}
            });
        });

    /**
     * Returns true if ANY model in the collection have a relationship with ANY of the given Models
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Boolean
     */
      Collection::macro('doesAnyHaveAnyOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAny($collection2)->isNotEmpty()) ? true : false;
      });
    /**
     * Returns true if ANY model in the collection have a relationship with ALL of the given Models
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Boolean
     */
      Collection::macro('doesAnyHaveAllOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAll($collection2)->isNotEmpty()) ? true : false;
      });
    
      /**
       * Returns true if ANY model in the collection have a relationship with ONLY the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesAnyHaveOnlyThese', function ($collection2) 
      {
        return ($this->filterIfHasOnly($collection2)->isNotEmpty()) ? true : false;
      });
      /**
       * Returns true if ALL models in the collection have a relationship with ANY of the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesAllHaveAnyOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAny($collection2)->count() == $this->count()) ? true : false;
      });
      /**
       * Returns true if ALL models in the collection have a relationship with ALL of the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesAllHaveAllOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAll($collection2)->count() == $this->count()) ? true : false;
      });
      /**
       * Returns true if ALL models in the collection have a relationship with ONLY the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesAllHaveOnlyThese', function ($collection2) 
      {
        return ($this->filterIfHasOnly($collection2)->count() == $this->count()) ? true : false;
      });
          /**
       * Returns true if ALL models in the collection have a relationship with ANY of the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesEveryHaveAnyOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAny($collection2)->count() == $collection2->count()) ? true : false;
      });
      /**
       * Returns true if ALL models in the collection have a relationship with ALL of the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesEveryHaveAllOfThese', function ($collection2) 
      {
        return ($this->filterIfHasAll($collection2)->count() == $collection2->count()) ? true : false;
      });
      /**
       * Returns true if ALL models in the collection have a relationship with ONLY the given Models
       *
       * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
       *
       * @return Boolean
       */
      Collection::macro('doesEveryHaveOnlyThese', function ($collection2) 
      {
        return ($this->filterIfHasOnly($collection2)->count() == $collection2->count()) ? true : false;
      });
    /**
     * Returns true if ANY model in the collection contain ANY of the given Permissions
     *
     * @param Mixed $collection2 /Collection of models to compare, MUST ALL BE ON THE SAME TABLE
     *
     * @return Boolean
     */
    }
}