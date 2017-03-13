<?php
	/**
	 * Project : Simpli
	 * Author : Agam Banga
	 * Created : 16/05/14
	 * Description : Behavior to handle Image
	 */
	class ImageSaveBehavior extends ModelBehavior {
		
/*
 * Purpose : The function will be manipulate data for images and save the images data in attachment table
 * Inputs :  $data – this data will contain model data and images data
 * Returns : returns true if successful in saving the data with its attachments else will return fail
 */ 
	public function save_data_with_attachments(Model $model, $data, $att_models = array())
	{
		try
		{
			$model->save($data);
			$last_insert_id = $model->id;
			if(!$last_insert_id) {
				return false;
			}
			$data[$model->alias]['id'] = $last_insert_id;
			foreach($att_models as $att)
			{
				if(!empty($data[$att]) && $data[$att]['image']['error'] == 0)
				{
					$data[$att]['model'] = $model->name;
					$data[$att]['foreign_key'] = $last_insert_id;
					$data[$att]['dir'] = $last_insert_id;
					$data[$att]['size'] = $data[$att]['image']['size'];
					$data[$att]['name'] = 'profile_' . $this->clean_string($data[$att]['image']['name']);
					$data[$att]['attachment_type'] = $att;
					$folder_name = Inflector::tableize($att);
					if(isset($data[$att]['id']))
					{
						// $this->delete_previous_files($data[$att], $folder_name);
					}
// 					creating the directory of new model uploads
					if (!file_exists(WWW_ROOT . 'uploads' . DS . $folder_name . DS))
					{
	    				mkdir(WWW_ROOT . 'uploads' . DS . $folder_name . DS , 0777, true);
					}
					if (!file_exists(WWW_ROOT . 'uploads' . DS .$folder_name . DS . $last_insert_id . DS ))
					{
	    				mkdir(WWW_ROOT . 'uploads' . DS . $folder_name . DS . $last_insert_id . DS , 0777, true);
					}
					$file_path = WWW_ROOT . 'uploads' . DS . $folder_name . DS . $last_insert_id . DS . $data[$att]['name'];
					@move_uploaded_file($data[$att]['image']['tmp_name'], $file_path);
					unset($data[$att]['image']);
				} else
				{
					if(isset($data[$att]))
					{
						unset($data[$att]);
					}
				}
			}
			if($model->saveAll($data)) {
				return true;
			} else {
				return false;
			}
		} catch(exception $excep)
		{
			return false;
		}
	}
	public function clean_string($string)
	{
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
	public function delete_previous_files($att_model, $folder_name)
	{
		$file_path = WWW_ROOT . 'uploads' . DS . $folder_name . DS . $att_model['dir'] . DS . $att_model['name'];
		pr($file_path);exit;
		@unlink($file_path);
	}
}
?>