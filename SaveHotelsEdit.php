<?php 

//Obtener todos los hoteles
    public static function getHotel($session, $country){
        $database = DatabaseFactory::getFactory()->getConnection();
        $hotel = $database->prepare("SELECT h.id_hotel, h.nombre_hotel,
        									pph.id_producto, pph.id_pais, pph.id_empresa
                                     FROM hotel as h, producto_pais_hotel as pph
                                     WHERE h.id_hotel = pph.id_hotel
                                       AND pph.id_producto = :sesion
                                       AND pph.id_pais = :pais;");
        $hotel->execute(array(':sesion' => $session,
                              ':pais'   => $country));

        if ($hotel->rowCount() > 0) {

        	$hoteles = $hotel->fetchAll();
        	$hotels = array();
        	$indice = 0;
        	foreach ($hoteles as $hotel) {
        		$hotels[$indice] = new stdClass();
        		$hotels[$indice]->id_hotel = $hotel->id_hotel;
        		$indice++;
        	}
        	//Guardo los hoteles activos actualmente
        	Session::set('hoteles', $hotels);
        	//Guardo la cantidad de hoteles activos
        	Session::set('indice', $indice);
       
            return $hotel->fetchAll();
        }

    }
    // Se obtiene el array de los hoteles que vienen de la vista
    public static function updateHotels($sesion, $pais, $hotels){
    	// Obtener los hoteles que se hayan desactivado
    	if (Session::get('hoteles')) {
    		//obtener la cantidad de hoteles activos previo
    		$activos = Session::get('indice');
    		$desactivados = array();
    		$nuevos		= array();
    		//Recorro cada uno de los hoteles que estaba activos.
    		for ($i=0; $i < $activos; $i++) { 
    			$previos = Session::get('hoteles')[$i];

    			//Consultar como recorrer el arreglo de $hoteles
    			for ($j=0; $j < count($hoteles); $j++) { 
    				if ($previo->id_hotel === $hoteles[$j]) {
    					$desactivados = array_push($desactivados, $previo->id_hotel);
    				} else {
    					//obtener los hoteles nuevos, que se daran de alta
    					$nuevos = array_push($nuevos, $hoteles[$j]);
    				}
    			}
    		} 
    	}
    	
    	
    	// Cambiar al estatus 0 los hoteles que ya no esten en
    	// la lista de hoteles activos.
    	
    	if (count($desactivados) > 0) {
    		for ($r=0; $r < count($desactivados); $r++) { 
    			$setStatus = $database->prepare("UPDATE producto_pais_hotel 
								    			 SET status = 0 
								    			 WHERE id_hotel = :hotel
								    			   AND id_pais  = :pais
								    			   AND id_producto = :sesion;");
				$setStatus->execute(array(':hotel'  => $desactivados[$r],
										  ':pais'   => $pais,
										  ':sesion' => $sesion));	
    		}
    	}

    	//Comprobar si dentro de la lista de hoteles nuevos
    	//no exista alguno que ya este en la BD, y que solo se actualizara.
    	if (count($nuevos)) {
    		$exist = array();
    		$new   = array();
    		for ($x=0; $x < count($nuevos); $x++) { 
    			$check = $database->prepare("SELECT id_hotel 
						    				 FROM producto_pais_hotel
						    				 WHERE id_hotel = :hotel
						    				   AND id_pais = :pais
						    				   AND id_producto = :sesion;");
    			$check->execute(array(':hotel' => $nuevos[$x],
    								  ':pais'  => $pais,
    								  ':sesion'=> $sesion));

    			if ($check->rowCount() > 0) {
    				$exist = array_push($exist, $nuevos[$x]);
    			} else {
    				$new = array_push($new, $nuevos[$x]);
    			}

    			//Actualizar los existentes en la BD
    			if (count($exist) > 0) {
    				for ($i=0; $i < count($exist); $i++) { 
    					$active = $database->prepare("UPDATE producto_pais_hotel
							    					  SET status = 1
							    					  WHERE id_hotel = :hotel
							    					    AND id_pais = :pais
							    					    AND id_producto = :sesion;");
	    				$active->execute(array(':hotel'  => $exist[$i],
						    			       ':pais'	=> $pais,
						    			       ':sesion' => $sesion));
    				}	
    			}

    			//insertar los nuevos hoteles con estatus 1
    			if (count($new) > 0) {
    				for ($j=0; $j < count($exist); $j++) { 
    					$active = $database->prepare("INSERT INTO producto_pais_hotel(id_producto,
															    					  id_pais,
															    					  id_hotel,
															    					  id_empresa)
															    		VALUES(:sesion, :pais, :hotel, empresa);");
	    				$active->execute(array(':hotel'  => $exist[$j],
						    			       ':pais'	=> $pais,
						    			       ':sesion' => $sesion,
						    			       ':empresa' => $empresa));
    				}
    			}

    		}
    	}
    	

    	
    }


?>