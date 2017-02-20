<?php

final class CityWeatherEuiController extends Base
{

    public function getCityWeatherList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new CityWeatherModel ();
        $ueObj->setField(' *,(select sName from website_type w where w.id=city) as city_name  ');
        $w = $this->getWhere();
        $res = $ueObj->getCityWeather($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getCityWeather($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_keyword = Run::req('_keyword');
        $_id = Run::req('_id');
        if (!$_keyword && !$_id) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_id) {
            $w .= ' and city="' . $_id . '" ';
        }
        return $w;
    }

    public function saveCityWeather()
    {
        $_id = Run::req('id');

        $dataArray ['city'] = Run::req('city');
        $listImg = $this->uploadImg($_FILES ['upload']);
        if ($listImg) {
            $dataArray ['img_path'] = $listImg;
        } else {
            $dataArray ['img_path'] = Run::req('img_path');
        }
        $dataArray ['zhangchao'] = Run::req('zhangchao');
        $dataArray ['tuichao'] = Run::req('tuichao');
        $dataArray ['richu'] = Run::req('richu');
        $dataArray ['riluo'] = Run::req('riluo');
        $dataArray ['wendu'] = Run::req('wendu');
        $dataArray ['tianqi'] = Run::req('tianqi');

        $sObj = new CityWeatherModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setCityWeather($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addCityWeather($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}