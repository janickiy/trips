<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Log;
use App\User;

/**
 *	Преобразователь артефактов
 */
class ArtifactMapper extends Controller
{
	private $degug = true;
	
    public function __construct()
    {
		
		
    }
	
	/**
	 *	Преобразователь одного артефакта:
	 */
	public static function cast($data, $info = null)
	{
		
		
		$artifactType = 0;
		
		if(is_array($data)) {
			$artifactType = $data['artifact_type'];
		}
		
		if(is_object($data)) {
			$artifactType =  $data->artifact_type;
		}
		
		if($artifactType == 0) {
			// TODO: log error!
			return false;
		}
		 
		
		

		switch ($artifactType) {
			case 4:
				$data = self::noteCast($data, $info);
		}
		
		return $data;
		
	
	}
	
	public static function bulkCast($data)
	{
		
	}
	
	/**
	 *	Note:
	 */
	public static function noteCast($data, $info = null)
	{

		if(is_array($data)) {

			if(isset($data["attributes"])) {
				if(isset($data["attributes"]["is_trip_description"])) {
					if($data["attributes"]["is_trip_description"] == 1) {
						$data["attributes"]["is_trip_description"] = true;
					} else if ($data["attributes"]["is_trip_description"] == 0) {
						$data["attributes"]["is_trip_description"] = false;
					} else {
						$data["attributes"]["is_trip_description"] = false;
					}
				}
			}

		}
		
		// Преобразование объекта:
		if(is_object($data)) {

			if(property_exists($data, 'attributes')) {
				
				// Для обычного объекта:
				if(property_exists($data->attributes, 'is_trip_description')) {

					if($data->attributes->is_trip_description == 1) {
						$data->attributes->is_trip_description = true;
					} else if ($data->attributes->is_trip_description == 0) {
						$data->attributes->is_trip_description = false;
					} else {
						$data->attributes->is_trip_description = false;
					}

				} else if(isset($data->attributes['is_trip_description'])) {
					// Для атрибутов виде коллекции:					
					if($data->attributes['is_trip_description'] == 1) {
						$data->attributes['is_trip_description'] = true;
					} else if ($data->attributes['is_trip_description'] == 0) {
						$data->attributes['is_trip_description'] = false;
					} else {
						$data->attributes['is_trip_description'] = false;
					}
					
				}
				
			}
			
		}

		return $data;
	}
	
	
}