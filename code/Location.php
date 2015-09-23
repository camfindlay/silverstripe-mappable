<?php

class Location extends DataObject {

	private static $db = array(
		'Name' => 'Text',
		'Address1' => 'Text',
		'Suburb' => 'Varchar',
		'City' => 'Varchar',
		//'Region' => "Enum('Northland,Auckland,Coromandel,Waikato,Bay Of Plenty,East Cape,Taupo,King Country,Taranaki,Hawkes Bay,Wanganui,Wellington,Wairarapa,Nelson,Marlborough,West Coast,Canterbury,Otago,Southland', 'Auckland')",
		'Region' => "Enum('Auckland,Bay Of Plenty,Canterbury,Coromandel,East Cape,Hawkes Bay,King Country,Marlborough,Nelson,Northland,Otago,Southland,Taranaki,Taupo,Waikato,Wairarapa,Wanganui,Wellington,West Coast', 'Auckland')",
		'InfoWindow' => 'HTMLText',
		'lat' => 'Double',
		'lng' => 'Double',
	);

	public static $summary_fields = array(
		'Name',
		'City',
		'Region'
	);

	public function getTitle() {
		return $this->Name;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeFieldsFromTab('Root.Main', array('Name', 'InfoWindow', 'lat', 'lng'));

		$fields->addFieldToTab('Root.Main', new TextField('Name', 'Name', $this->Name), 'Address1');
		$fields->addFieldToTab('Root.Main', $infoWindow = HtmlEditorField::create('InfoWindow'));
		$infoWindow->setRows(8); // limit the height of the editor
		$fields->addFieldToTab('Root.Main', new TextField('lat', 'Latitude', $this->lat), 'InfoWindow');
		$fields->addFieldToTab('Root.Main', new TextField('lng', 'Longitude', $this->lng), 'InfoWindow');

		return $fields;
	}

	public function onBeforeWrite() {
//		if (!$this->lat || !$this->lng || $this->isChanged('Address1')) {

		if (!$this->lat || !$this->lng) {
			if ($this->Address1) {
			//if ($this->Address1 && $this->Suburb && $this->Region) {
				// Get the member's address
				//$address = $this->Address1 . ' ' . $this->Suburb . ' ' . $this->Region;
				$address = $this->Address1;
				// Run it through GoogleGeocoding's address_to_point function (RestfulService/XML)
				$point = GoogleGeocoding::address_to_point($address);
				// Set the lat and Longitude values with the response
				$this->lat = $point['Latitude'];
				$this->lng = $point['Longitude'];
			}
			parent::onBeforeWrite();
		}
	}

}
