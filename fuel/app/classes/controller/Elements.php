<?php
use \Model\Elements;
use Firebase\JWT\JWT;
class Controller_Elements extends Controller_Base
{
	private  $idAdmin = 1;
    

	public function post_create()
	{
	
	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	if($arrayAuthenticated['authenticated'])
    	{
    		$decodedToken = $this->decode($arrayAuthenticated['data']);
    		if ($decodedToken->id == ID_ADMIN){
			try {


				if(isset($_POST['x']) || isset($_POST['y'])){
				    if(empty($_POST['x']) || empty($_POST['y'])){
					    return $this->respuesta(400, 'Coordenadas vacias', '');
					}
				}
				else
				{
				    return $this->respuesta(400, 'Coordenadas no definidas', '');
				}


				
				if ( !isset($_POST['name']) || !isset($_POST['description'])) 
				{
				    
				
					if(!empty($_POST['name']) && !empty($_POST['description']))
					{
					            	
						$input = $_POST;
						$newElement = $this->newElement($input);
						$json = $this->saveElement($newElement);
					}
					else
					{
			    		return $this->respuesta(400, 'Algun campo vacio', '');
					}
				}
			}

	        $config = array(
			    'path' => DOCROOT . 'assets/img',
			    'randomize' => true,
			    'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
			);

			Upload::process($config);
			$photo = "";
			if (Upload::is_valid())
			{
			    Upload::save();
			    foreach(Upload::get_files() as $file)
			    {
			            	
			    $photo = 'http://localhost:8888/ARAPI/public/assets/img/'. $file['saved_as'];
			            	
				}
			}

	        foreach (Upload::get_errors() as $file)
	        {
			    return $this->response(array(
			        'code' => 500,
			        'message' => 'Error en el servidor',
			        'data' => '' 
			        ));
	       }

	       
	       	$input = $_POST;
			$newElement = $this->newElement($input, $photo, $decodedToken);
			$json = $this->saveElement($newElement);
		    return $json;


			catch (Exception $e)
			{
				return $this->respuesta(500, $e->getMessage(), '');
			}
			}
			else 
			{
				return $this->respuesta(400, 'No eres el admin', '');
			}      
		}
	}

	private function newElement($input)
	{
		$element = Model_Elements();
		$element->name = $input['name'];
		$element->description = $input['description'];
		$element->photo = "";
		$element->x = $input['x'];
		$element->y = $input['y'];
		$element->id_type = $input['id_type'];
		$element->id_user = $this->idAdmin;
		return $element;
	}

	private function saveElement($element)
    {
    	$elementExists = Model_Elements::find('all', 
    								array('where' => array(
    													array('name', '=', $element->name),
    														)
    									)
    							);
    	if(empty($elementExists)){
    		$elementToSave = $element;
    		$elementToSave->save();
    		$arrayData = array();
    		$arrayData['name'] = $element->name;
    		return $this->respuesta(201, 'Elemento creado', $arrayData);
    	}else{
    		return $this->respuesta(204, 'Elemento ya creado', '');
    	}
    }



}