<?php
/**
 * 日历配置项
 */
return [
    // 默认配置
    'default' => [

        // 读取日历长度
        // 0 GRID_DAY 一天
        // 1 GRID_WEEK 一周
        // 2 GRID_MONTH 一个月
        'grid' => \phpu\calendar\Calendar::GRID_MONTH,

        // 读取节气
        'solar_terms' => true,

        // 读取干支
        'heavenly_earthly' => true,

        // 读取农历
        'lunar' => true,

        // 区分早晚子时，true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
        'night_zi_hour' => false,
    ],
    // 自定义多个配置
    // 在new对象时加上参数键名作为配置名
    // 使用方法: $Calendar = new Calendar('demo');
    'demo'=>[
        'grid' => 1,
        'solar_terms' => true,
        'heavenly_earthly' => false,
        'lunar' => false,
    ],


];
