<?php
	/**
	 * Project : FNB
	 * Author : Agam Banga
	 * Created : 11/Sep/2014
	 * Description : Behavior to handle slugs creation
	 */
	class SlugBehavior extends ModelBehavior {
		
		/**
		 * Purpose: Slugs creation after saving any item
		 */
		function afterSave(Model $model, $created, $options = array()) 
		{
			if($created)
			{
				$string = '';
				if(isset($model->data[$model->name][$model->displayField]))
				{
					$string = $model->data[$model->name][$model->displayField];
					$string = substr($string, 0, 100);
					$slug = $this->createSlug($string, $model->id);
					$model->save(array($model->name => array('id' => $model->id, 'slug' => $slug)), array('callbacks' => false));
				}
			}
			return true;
		}
		
		/**
		 * Purpose: To create unique slugs
		 * in: $string (slug), $id
		 * out: slug
		 */
		 public function createSlug ($string, $id) 
		 {
			$slug = Inflector::slug ($string,'-');
			$slug = strtolower ($slug) . "-" .  $id;
			return $slug;
		}
	}
?>