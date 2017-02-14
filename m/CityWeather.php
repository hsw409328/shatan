<?php
final class CityWeatherModel extends Mysql {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCityWeather($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `city_weather` ';
		if (! empty ( $where )) {
			$sql .= ' where ' . $where;
		}
		if (! empty ( $orderby )) {
			$sql .= ' order by ' . $orderby;
		} else {
			$sql .= ' order by id desc ';
		}
		if (! empty ( $limit )) {
			$sql .= ' limit ' . $limit;
		}
		if (empty ( $solo )) {
			$res = $this->get_all ( $sql );
		} else {
			$res = $this->get_one ( $sql );
		}
		return $res;
	}
	
	public function addCityWeather($data) {

		$res = $this->insert ( 'city_weather', $data );
		return $res;
	}
	
	public function setCityWeather($id, $dataArray) {
		$res = $this->update ( 'city_weather', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCityWeather($id) {
		$res = $this->delete ( 'city_weather', ' id="' . $id . '" ' );
		return $res;
	}
}
