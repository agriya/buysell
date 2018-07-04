<?php
class XmlParser
	{   
	    public $arrOutput = array();
	    public $resParser;
	    public $strXmlData;
	   
	    public function parse($strInputXML)
			{	   
	            $this->resParser = xml_parser_create ();
	            xml_set_object($this->resParser,$this);
	            xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
	           
	            xml_set_character_data_handler($this->resParser, "tagData");
	       
	            $this->strXmlData = xml_parse($this->resParser,$strInputXML );
	            if(!$this->strXmlData) {
	               return false;
				   die(sprintf("XML error: %s at line %d",
	            xml_error_string(xml_get_error_code($this->resParser)),
	            xml_get_current_line_number($this->resParser)));
	            }	                           
	            xml_parser_free($this->resParser);	           
	            return $this->arrOutput;
	    	}
			
	    public function tagOpen($parser, $name, $attrs)
			{
	       		$tag=array("name"=>$name,"attrs"=>$attrs);
	       		array_push($this->arrOutput,$tag);
	    	}
	   
	   public function tagData($parser, $tagData)
			{
		       if(trim($tagData)) {
		            if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
		                $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
		            }
		            else {
		                $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
		            }
		       }
	   		}
	   
	   public function tagClosed($parser, $name)
			{
		       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
		       array_pop($this->arrOutput);
		    }
	}
?>