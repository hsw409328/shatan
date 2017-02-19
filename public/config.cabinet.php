<?php

/**
 * 柜子格子的布局
 * User: MrHao
 * Date: 2017/2/13
 * Time: 10:11
 */
class ConfigCabinet
{

    // 回收箱 21 => 'A21',
    public static $config = [
        'A' => [
            1 => 'A1', 2 => 'A2', 3 => 'A3',
            4 => 'A4', 5 => 'A5', 6 => 'A6',
            7 => 'A7', 8 => 'A8', 9 => 'A9',
            10 => 'A10', 11 => 'A11',
            12 => 'A12', 13 => 'A13',
            14 => 'A14', 15 => 'A15',
            16 => 'A16', 17 => 'A17',
            18 => 'A18', 19 => 'A19',
            20 => 'A20',
        ],
        'B' => [
            1 => 'B1', 2 => 'B2', 3 => 'B3',
            4 => 'B4', 5 => 'B5', 6 => 'B6',
            7 => 'B7', 8 => 'B8', 9 => 'B9',
            10 => 'B10', 11 => 'B11',
            12 => 'B12', 13 => 'B13',
            14 => 'B14', 15 => 'B15',
            16 => 'B16', 17 => 'B17',
            18 => 'B18', 19 => 'B19',
            20 => 'B20', 21 => 'B21',
            22 => 'B22',
        ],
        'C' => [
            1 => 'C1', 2 => 'C2', 3 => 'C3',
            4 => 'C4', 5 => 'C5', 6 => 'C6',
            7 => 'C7', 8 => 'C8', 9 => 'C9',
            10 => 'C10', 11 => 'C11',
            12 => 'C12', 13 => 'C13',
            14 => 'C14', 15 => 'C15',
            16 => 'C16', 17 => 'C17',
            18 => 'C18', 19 => 'C19',
            20 => 'C20', 21 => 'C21',
            22 => 'C22',
        ],
        'D' => [
            1 => 'D1', 2 => 'D2', 3 => 'D3',
            4 => 'D4', 5 => 'D5', 6 => 'D6',
            7 => 'D7', 8 => 'D8', 9 => 'D9',
            10 => 'D10', 11 => 'D11',
            12 => 'D12', 13 => 'D13',
            14 => 'D14', 15 => 'D15',
            16 => 'D16', 17 => 'D17',
            18 => 'D18', 19 => 'D19',
            20 => 'D20', 21 => 'D21',
            22 => 'D22',
        ],
    ];

    /**
     * 获取所有柜子类型
     */
    public static function getAllKey()
    {
        return array_keys(self::$config);
    }

    /**
     * 获取全部柜子的格局
     * @param $_key
     */
    public static function getAllGrid()
    {
        return self::$config;
    }

    /**
     * 根据组获取格子
     * @param $group
     */
    public static function getAllGridByGroup($group)
    {
        $len = strlen($group);
        if ($len <= 0) {
            return [];
        }
        $_tmp = [];
        for ($i = 0; $i < $len; $i++) {
            $rs = self::getOneGrid($group[$i]);
            foreach ($rs as $k => $v) {
                $_tmp[] = $v;
            }
        }
        return $_tmp;
    }

    /**
     * 获取单个柜子的格局
     * @param $_key
     */
    public static function getOneGrid($_key)
    {
        return isset(self::$config[$_key]) ? self::$config[$_key] : null;
    }

}
