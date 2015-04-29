<?php
class FileComponent extends Object {
	var $controller;
	
	function upload($formdata, $entry_type, $rewrite = false) {
		// setup dir names absolute and relative  
		$folder_url = WWW_ROOT . 'files';
		$rel_url = 'files';
		$result = array();
		
		// list of permitted file types, this is only images but documents can be added  
    	$permitted = array(
			'xml' => array(
				'text/xml'
			)
		);
		// loop through and deal with the files  
		foreach($formdata as $file) {
			// replace spaces with underscores  
			$filename = str_replace(' ', '_', $file['name']);  
			// assume filetype is false  
			$typeOK = false;  
			// check filetype is ok
			if($entry_type == 'xml') {
				$filename = 'orden_compra.xml';
			}
			else {
				return;
			}
			foreach($permitted[$entry_type] as $type) {  
				if($type == $file['type']) {  
					$typeOK = true;  
					break;  
				}  
			}
			
			// if file type ok upload the file  
        	if($typeOK) {
				// switch based on error code
				switch($file['error']) {
					case 0:
						// check filename already exists  
						if(!file_exists($folder_url . DS . $filename) or $rewrite) {  
							// create full filename  
							$full_url = $folder_url . DS . $filename;  
							$url = $rel_url . DS . $filename;  
							// upload the file  
							$success = move_uploaded_file($file['tmp_name'], $full_url);  
						} else {  
							// create unique filename and upload file  
							ini_set('date.timezone', 'America/Santiago');  
							$now = date('Y-m-d-His');  
							$full_url = $folder_url . DS . $now . $filename;  
							$url = $rel_url . DS . $now . $filename;
							$filename = $now . $filename;
							$success = move_uploaded_file($file['tmp_name'], $url);  
                    	}
						$url = $filename;
						// if upload was successful  
						if($success) {  
							// save the url of the file  
							$result['urls'][] = $url;  
						} else {  
							$result['errors'][] = "Error uploaded $filename. Please try again.";  
						}  
						break;
					
					case 3:
						// an error occured  
						$result['errors'][] = "Error uploading $filename. Please try again.";  
						break;					
					default:	 
						// an error occured  
						$result['errors'][] = "System error uploading $filename. Contact webmaster.";  
						break;
				}				
			} elseif($file['error'] == 4) {  
				// no file was selected for upload  
				$result['nofiles'][] = "No file Selected";  
			} else {  
				// unacceptable file type  
				$result['errors'][] = "$filename cannot be uploaded. not Acceptable.";  
			}		
		}	 
		return $result;
	}
	
	function delete($folder, $fileName) {
		if (unlink($folder . DS . $fileName)) {
			return true;
		} else {
			return false;
		}
	}
        
        //Subir PDF de OC a la carpeta en webroot/pdf
        function upload_pdf($file, $id, $type_doc='DP'){
            $path_base = WWW_ROOT . 'pdf' . DS;
            $file_name = $type_doc . '_' . $id . '.pdf';
            $full_path = $path_base . $file_name;
            if($file['type'] == 'application/pdf' && !file_exists($full_path)){
                return move_uploaded_file($file['tmp_name'], $full_path);
            }else{
                return false;
            }
        }
	//Sube dcto CSV del proveedor para poder procesarlo
        function upload_csv($file, $id, $type_doc='CSV'){
            $path_base = WWW_ROOT . 'csv' . DS;
            $file_name = $type_doc . '_' . $id . '.csv';
            $full_path = $path_base . $file_name;
            if(in_array($file['type'], array('text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel'))){
                move_uploaded_file($file['tmp_name'], $full_path);
                return $full_path;
            }else{
                return false;
            }
        }
}	
?>