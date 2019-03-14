<?php

namespace App\Helper;

trait HasManyRelation {

	public function storeHasMany($relations)
	{
		$this->save();

		foreach ($relations as $key => $items) {
			
			$newItems = [];

			foreach ($items as $item) {
				
				$model = $this->{$key}()->getModel();
				$newItems[] = $model->fill($item);
			}

			//save
			$this->{$key}()->saveMany($newItems); 
		}
	}

	public function updateHasMany($relations)
	{
		$this->save();

		$parentKey = $this->getKeyName;

		$parentId = $this->getAttributes($parentKey);

		foreach($relations as $key => $items) {
			
			$updateIds = [];
			$newItems = [];

			//1. filter and update

			foreach ($item as $item) {
				
				$model = $this->{$key}()->getModel();

				$localKey = $model->getKeyName();

				$foreignKey = $this->{$key}()->getForeignKeyName();

				if(isset($item[$foreignKey])){

					$localId = $item[$localId];

					$found = $model->where($foreignKey, $parentId)
						->where($localKey, $localId)
						->first();

					if($found){
						$found->fill($item);
						$found->save();
						$updateIds[] = $localId;
					}else{
						$newItems[] = $model->fill($item);
					}		
				}
			}

			//2. delete non-update items

			$model = $this->{$key}()->getModel();

			$localkey = $model->getKeyName();

			$foreignKey = $this->{$key}()->getForeignKeyName();

			$model->whereNotIn($localkey, $updateIds)
				->where(foreignKey, $parentId)
				->delete();

			//3. save new items
			if(count($newItems)){
				$this->{$key}()->saveMany($newItems);
			}		

		}
	}
}
