<?php

declare(strict_types=1);

namespace phpu\calendar;

use \Exception;

/**
 * 节气相关
 *
 * Class SolarTerm
 *
 * @package phpu\calendar
 */
class SolarTerm
{
    /**
     * 指定年份以春分开始的节气
     *
     * @var array  以年份数为索引的节气数组
     */
    private static $msts = [];

    /**
     * 从上一年的冬至开始到下一年的小寒共26个节气对应的日期时间，
     * 目的是取出当前格里历一年完整的节气，
     *
     * @param int    $year         指定的年份数(年份限-1000至3000)
     * @param string $timeZoneName 时区名称 默认: 'Asia/Shanghai' (亚洲/上海，中国北京时间)
     *
     * @return array array[26][array['i'=>int节气名称索引,'d'=>DateTime节气对应的日期时间]]
     * @throws Exception
     */
    public static function solarTerms(int $year, string $timeZoneName = 'Asia/Shanghai' ):array
    {
        if ($year < -1000 || $year > 3000){
            throw new \InvalidArgumentException('年份限-1000至3000');
        }

        $jq = [];
        $jqnum = 0;
        // $jqIndexformat = "%'.04d-%'.02d-%'.02d";
        $jqIndexformat = 'Y-n-j';

        $lastYearAsts = self::lastYearSolarTerms($year);
        foreach ($lastYearAsts as $k => $v){
            if($k < 18){
                continue;
            }
            if($k > 23){
                continue;
            }

            $jqdata['i'] = ($jqnum + 18) % 24;
            $jqnum ++;

            // $v += $offsetDays; // 按时区调整
            $dt = Julian::jdToDateTime($v,$timeZoneName);
            $jqdata['d'] = $dt;
            $jqak = $dt->format($jqIndexformat);
            $jq[$jqak] = $jqdata;
        }

        $asts = self::adjustedSolarTerms($year, 0, 19);
        foreach ($asts as $k => $v){

            // 节气名称索引，对应0:春分,1:清明,2:谷雨,3:立夏,4:小满,5:芒种......18:冬至,19:小寒,20:大寒,21:立春,22:雨水,23:惊蛰
            $jqdata['i'] = ($jqnum + 18) % 24;
            $jqnum ++;

            $dt = Julian::jdToDateTime($v,$timeZoneName);
            $jqdata['d'] = $dt;
            $jqak = $dt->format($jqIndexformat);
            $jq[$jqak] = $jqdata;
        }

        return $jq;
    }

    /**
     * 获取指定年以春分开始的节气,
     * 经过摄动值和deltaT调整后的jd
     *
     * @param int $year 指定的年份数
     * @param int $start 取节气开始数
     * @param int $end 取节气结束数
     *
     * @return array array[]float [儒略日...]
     */
    public static function adjustedSolarTerms(int $year, int $start=0, int $end=25):array
    {
        if(isset(self::$msts[$year])){
            $mst = self::$msts[$year];
        }else{
            $mst = Astronomy::meanSolarTerms($year);
            self::$msts[$year] = $mst;
        }

        $jq = [];
        foreach ($mst as $i => $jd){
            if($i < $start){
                continue;
            }
            if($i > $end){
                continue;
            }

            $pert = Astronomy::perturbation($jd); // perturbation
            $dtd = Astronomy::deltaTDays($year, floor(($i+1) / 2) + 3); // delta T(天)
            $jq[$i] = $jd + $pert - $dtd; // 加上摄动调整值ptb,减去对应的Delta T值(分钟转换为日)
        }

        return $jq;
    }

    /**
     * 取出上一年从冬至开始的6个节气
     *
     * @param int $year 当前年，方法内会自动减1
     *
     * @return array array[6]float  [儒略日...]
     */
    public static function lastYearSolarTerms(int $year):array
    {
        return self::adjustedSolarTerms($year - 1, 18, 23);
    }



}