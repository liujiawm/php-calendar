<?php
/**
 * 日历配置项
 */
return [
    'default' => [

        // 读取日历长度
        // 0 GRIDO_MONTH 一个月
        // 1 GRIDO_WEEK 一周
        // 2 GRIDO_DAY 一天
        'grid' => \phpu\calendar\Calendar::GRIDO_MONTH,

        // 读取节气
        'solar_terms' => true,

        // 读取农历
        'lunar' => true,

        // 读取干支
        'heavenly_earthly' => true,

        // 区分早晚子时，true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
        'night_zi_hour' => false,

        // 日历显示时第一列显示周几，(日历表第一列是周几,0周日,依次最大值6)
        'first_day_of_week' => 0,
    ],
    // 不同的独立配置可另行设置
    // 'demo'=>[
    //    'grid' => \phpu\calendar\Calendar::GRIDO_DAY,
    //    'solar_terms' => true,
    //    'lunar' => false,
    //    'heavenly_earthly' => false,
    // ],


];
