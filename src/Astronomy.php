<?php

declare(strict_types=1);

namespace phpu\calendar;

use InvalidArgumentException;

/**
 * 天文相关
 *
 * Class Astronomy
 *
 * @package phpu\calendar
 */
class Astronomy
{
    /**
     * 均值朔望月长(mean length of synodic month)
     * @var float
     */
    public const MSM = 29.530588853;


    /**
     * 以2000年的第一个均值新月点为基准点，此基准点为2000年1月6日14时20分37秒(TT)，其对应真实新月点为2000年1月6日18时13分42秒(TT)
     * public const BNM = 2451550.0976504628;
     * public const BNM = 2451550.09765046;
     * public const BNM = 2451550.09765;
     * php7的float是16位(包括小数点)有效数,该值转为2000年1月6日14时20分36秒(TT)
     *
     * @var float
     */
    public const BNM = 2451550.0976504628;

    /**
     * 地球自转速度调整值Delta T(以∆T表示)
     * 地球时和UTC的时差 单位:天(days)
     * 精确至月份
     *
     * @param int   $year  年
     * @param float $month 月
     *
     * @return float ∆T 单位:天(days)
     */
    public static function deltaTDays(int $year,float $month):float
    {
        $dt = self::deltaTSeconds($year, $month);
        return $dt / 60.0 / 60.0 / 24.0; // 将秒转换为天
    }

    /**
     * 地球自转速度调整值Delta T(以∆T表示)
     * 地球时和UTC的时差 单位:分(minutes)
     * 精确至月份
     *
     * @param int   $year  年
     * @param float $month 月
     *
     * @return float ∆T 单位:分(minutes)
     */
    public static function deltaTMinutes(int $year,float $month):float
    {
        $dt = self::deltaTSeconds($year, $month);
        return $dt / 60.0; // 将秒转换为分
    }

    /**
     * 地球自转速度调整值Delta T(以∆T表示)
     * 地球时和UTC的时差 单位:秒(seconds)
     * 精确至月份
     *
     * @param int $year    年
     * @param float $month 月
     *
     * @return float ∆T 单位:秒(seconds)
     */
    public static function deltaTSeconds(int $year,float $month):float
    {
        // 计算方法参考: https://eclipse.gsfc.nasa.gov/SEhelp/deltatpoly2004.html
        // 此算法在-1999年到3000年之间有效

        if($year < -1999 || $year > 3000){
            throw new InvalidArgumentException('计算DeltaT值限-1999年至3000年之间有效');
        }

        $year  = (float)$year;  // 转成float

        $y = $year + ($month - 0.5) / 12;

        if ($year < -500) {
            $u = ($year - 1820) / 100.0;
            $dt = -20 + 32 * $u * $u;
        }else if($year < 500){
            $u = $y / 100;
            $dt = 10583.6
                - 1014.41 * $u
                + 33.78311 * pow($u, 2)
                - 5.952053 * pow($u, 3)
                - 0.1798452 * pow($u, 4)
                + 0.022174192 * pow($u, 5)
                + 0.0090316521 * pow($u, 6);
        }else if($year < 1600){
            $u = ($y - 1000) / 100;
            $dt = 1574.2
                - 556.01 * $u
                + 71.23472 * pow($u, 2)
                + 0.319781 * pow($u, 3)
                - 0.8503463 * pow($u, 4)
                - 0.005050998 * pow($u, 5)
                + 0.0083572073 * pow($u, 6);
        }else if($year < 1700){
            $t = $y - 1600;
            $dt = 120
                - 0.9808 * $t
                - 0.01532 * pow($t, 2)
                + pow($t,3) / 7129;
        }else if($year < 1800){
            $t = $y - 1700;
            $dt = 8.83
                + 0.1603 * $t
                - 0.0059285 * pow($t, 2)
                + 0.00013336 * pow($t, 3)
                - pow($t, 4) / 1174000;
        }else if($year < 1860){
            $t = $y - 1800;
            $dt = 13.72
                - 0.332447 * $t
                + 0.0068612 * pow($t, 2)
                + 0.0041116 * pow($t, 3)
                - 0.00037436 * pow($t, 4)
                + 0.0000121272 * pow($t, 5)
                - 0.0000001699 * pow($t, 6)
                + 0.000000000875 * pow($t, 7);
        }else if($year < 1900){
            $t = $y - 1860;
            $dt = 7.62
                + 0.5737 * $t
                - 0.251754 * pow($t, 2)
                + 0.01680668 * pow($t, 3)
                - 0.0004473624 * pow($t, 4)
                + pow($t,5) / 233174;
        }else if($year < 1920){
            $t = $y - 1900;
            $dt = -2.79
                + 1.494119 * $t
                - 0.0598939 * pow($t, 2)
                + 0.0061966 * pow($t, 3)
                - 0.000197 * pow($t, 4);
        }else if($year < 1941){
            $t = $y - 1920;
            $dt = 21.2
                + 0.84493 * $t
                - 0.0761 * pow($t, 2)
                + 0.0020936 * pow($t, 3);
        }else if($year < 1961){
            $t = $y - 1950;
            $dt = 29.07
                + 0.407 * $t
                - pow($t, 2) / 233
                + pow($t, 3) / 2547;
        }else if($year < 1986){
            $t = $y - 1975;
            $dt = 45.45
                + 1.067 * $t
                - pow($t, 2) / 260
                - pow($t, 3) / 718;
        }else if($year < 2005){
            $t = $y - 2000;
            $dt = 63.86
                + 0.3345 * $t
                - 0.060374 * pow($t, 2)
                + 0.0017275 * pow($t, 3)
                + 0.000651814 * pow($t, 4)
                + 0.00002373599 * pow($t, 5);
        }else if($year < 2050){
            $t = $y - 2000;
            $dt = 62.92
                + 0.32217 * $t
                + 0.005589 * pow($t, 2);
        }else if($year < 2150){
            $u = ($y - 1820) / 100;
            $dt = -20
                + 32 * pow($u,- 2)
                - 0.5628 * (2150 - $y);
        }else {
            $u = ($y - 1820) / 100;
            $dt = -20 + 32 * pow($u, 2);
        }

        // 以上的∆T值均假定月球的长期加速度为-26弧秒/cy^2
        // 而Canon中使用的ELP-2000/82月历使用的值略有不同，为-25.858弧秒/cy^2
        // 因此，必须在∆T多项式表达式得出的值上加上一个小的修正“c”，然后才能将其用于标准中
        // 由于1955年至2005年期间的ΔT值是独立于任何月历而得出的，因此该期间无需校正。
        if ($year < 1955 || $year >= 2005){
            $c = -0.000012932 * ($y - 1955) * ($y - 1955);

            $dt += $c;
        }

        return (float)$dt;
    }

    /**
     * 地球在绕日运行时会因受到其他星球之影响而产生摄动(perturbation)
     *
     * @param float $jd 儒略日
     *
     * @return float
     */
    public static function perturbation(float $jd):float
    {
        // 算法公式摘自Jean Meeus在1991年出版的《Astronomical Algorithms》第27章 Equinoxes and solsticesq (第177页)
        // http://www.agopax.it/Libri_astronomia/pdf/Astronomical%20Algorithms.pdf
        // 公式: 0.00001S/∆λ
        // S = Σ[A cos(B+CT)]
        // B和C的单位是度
        // T = JDE0 - J2000 / 36525
        // J2000 = 2451545.0
        // 36525是儒略历一个世纪的天数
        // ∆λ = 1 + 0.0334cosW+0.0007cos2W
        // W = (35999.373T - 2.47)π/180
        // 注释: Liu Min<liujiawm@163.com> https://github.com/liujiawm


        // 公式中A,B,C的值
        $ptsA = [485, 203, 199, 182, 156, 136, 77, 74, 70, 58, 52, 50, 45, 44, 29, 18, 17, 16, 14, 12, 12, 12, 9, 8];
        $ptsB = [324.96, 337.23, 342.08, 27.85, 73.14, 171.52, 222.54, 296.72, 243.58, 119.81, 297.17, 21.02, 247.54,
                 325.15,60.93, 155.12, 288.79, 198.04, 199.76, 95.39, 287.11, 320.81, 227.73, 15.45];
        $ptsC = [1934.136, 32964.467, 20.186, 445267.112, 45036.886, 22518.443, 65928.934, 3034.906, 9037.513,
                 33718.147, 150.678, 2281.226, 29929.562, 31555.956, 4443.417, 67555.328, 4562.452, 62894.029, 31436.921,
                 14577.848, 31931.756, 34777.259, 1222.114, 16859.074];

        $T = Julian::julianCentury($jd); // $T是以儒略世纪(36525日)为单位，以J2000(儒略日2451545.0)为0点

        $s = (float)0;
        for ($k = 0; $k <= 23; $k++) {
            // $s = $s + $ptsA[$k] * cos($ptsB[$k] * 2 * M_PI / 360 + $ptsC[$k] * 2 * M_PI / 360 * $t);
            $s = $s + $ptsA[$k] * cos(deg2rad($ptsB[$k]) + deg2rad($ptsC[$k]) * $T);
        }

        //$w = 35999.373 * $t - 2.47;
        // $l = 1 + 0.0334 * cos($w * 2 * M_PI / 360) + 0.0007 * cos(2 * $w * 2 * M_PI / 360);
        $W = deg2rad(35999.373 * $T - 2.47);
        $l = 1 + 0.0334 * cos($W) + 0.0007 * cos(2 * $W);

        return 0.00001 * $s / $l;
    }


    /**
     * 计算指定年的春分点
     *
     * @param int $year
     *
     * @return float 春分点的儒略日
     */
    public static function vernalEquinox(int $year):float
    {

        // 算法公式摘自Jean Meeus在1991年出版的《Astronomical Algorithms》第27章 Equinoxes and solsticesq (第177页)
        // http://www.agopax.it/Libri_astronomia/pdf/Astronomical%20Algorithms.pdf
        // 此公式在-1000年至3000年之间比较准确
        // 在公元前1000年之前或公元3000年之后也可以延申使用，但因外差法求值，年代越远，算出的结果误差就越大。

        if ($year >= 1000 && $year <= 3000) {
            $m = ($year - 2000) / 1000.0;
            return 2451623.80984 + 365242.37404 * $m + 0.05169 * pow($m,2) - 0.00411 * pow($m,3) - 0.00057 * pow($m,4);
        }else{
            $m = $year / 1000.0;
            return 1721139.29189 + 365242.1374 * $m + 0.06134 * pow($m,2) + 0.00111 * pow($m,3) - 0.00071 * pow($m,4);
        }
    }

    /**
     * 指定年的回归年长
     * 两个春分点之间为一个回归年长
     *
     * @param int $year
     *
     * @return float 回归年长 (天)
     */
    public static function tropicalYearDays(int $year):float
    {
        $ve = self::vernalEquinox($year);

        $nextYear = $year + 1;
        $veNextYear = self::vernalEquinox($nextYear);
        return $veNextYear - $ve;
    }

    /**
     * 获取指定年以春分开始的24节气(为了确保覆盖完一个公历年，该方法多取2个节气),
     * 注意：该方法取出的节气时间是未经微调的。
     *
     * @param int $year 年份数字
     *
     * @return array map[int]float
     */
    public static function meanSolarTerms(int $year):array{

        // 该年的春分点jd
        $ve = self::vernalEquinox($year);

        // 该年的回归年长(天)
        // 两个春分点之间为一个回归年长
        $ty = self::vernalEquinox($year + 1) - $ve;

        // 多取2个节气确保覆盖完一个公历年 24+2
        $stNum = 26;

        // 以春分点为起点，可在轨道上每隔15度取一个点，将轨道划分为24个区，这24个区就是24节气。
        // 由Kepler's second law (law of areas)定律可知，
        // 地球在绕日的轨道上，单位时间内扫过的面积是固定值，因此，在不同节气内，地球的运行速度是不同的，
        // 在近日点附近的速度较快，在远日点附近的速度较慢

        $ath = 2 * M_PI / 24;

        $T = Julian::julianThousandYear($ve); // 计算标准历元起的儒略千年数
        $e = 0.0167086342
            - 0.0004203654 * $T
            - 0.0000126734 * pow($T,2)
            + 0.0000001444 * pow($T,3)
            - 0.0000000002 * pow($T,4)
            + 0.0000000003 * pow($T,5);

        // 春分点与近日点之夹角(度)
        $TT = $year / 1000;
        $d = 111.25586939
            - 17.0119934518333 * $TT
            - 0.044091890166673 * pow($TT,2)
            - 4.37356166661345E-04 * pow($TT,3)
            + 8.16716666602386E-06 * pow($TT,4);
        // 将角度转成弧度
        $rvp = deg2rad($d);

        $peri = [];
        for ($i = 0; $i <= $stNum; $i++) {
            $flag = 0;
            $th = $ath * $i + $rvp;
            if ($th > M_PI && $th <= 3 * M_PI) {
                $th = 2 * M_PI - $th;
                $flag = 1;
            }
            if ($th > 3 * M_PI) {
                $th = 4 * M_PI - $th;
                $flag = 2;
            }

            $f1 = 2 * atan((sqrt((1 - $e) / (1 + $e)) * tan($th / 2)));
            $f2 = ($e * sqrt(1 - $e * $e) * sin($th)) / (1 + $e * cos($th));
            $f = ($f1 - $f2) * $ty / 2 / M_PI;
            if ($flag == 1){
                $f = $ty - $f;
            }
            if ($flag == 2){
                $f = 2 * $ty - $f;
            }
            $peri[$i] = $f;
        }

        $mst = [];
        for ($i = 0; $i < $stNum; $i++) {
            $mst[$i] = $ve + $peri[$i] - $peri[0];
        }

        return $mst;
    }



    /**
     * 求出实际新月点,
     * 以k值代入求瞬时朔望日
     *
     * @param float $k
     *
     * @return float
     */
    public static function trueNewMoon(float $k):float
    {
        // 对于指定的日期时刻JD值jd,算出其为相对于基准点(之后或之前)的第k个朔望月之内。
        // k=INT(jd-bnm)/msm
        // 新月点估值(new moon estimated)为：nme=bnm+msm×k
        // 估计的世纪变数值为：t = (nme - J2000) / 36525
        // 此t是以2000年1月1日12时(TT)为0点，以100年为单位的时间变数，
        // 由于朔望月长每个月都不同，msm所代表的只是其均值，所以算出新月点后，还需要加上一个调整值。
        // adj = 0.0001337×t×t - 0.00000015×t×t×t + 0.00000000073×t×t×t×t
        // 指定日期时刻所属的均值新月点JD值(mean new moon)：mnm=nme+adj

        $nme = self::BNM + self::MSM * $k;

        $t = Julian::julianCentury($nme);
        $t2 = pow($t,2); // square for frequent use
        $t3 = pow($t,3); // cube for frequent use
        $t4 = pow($t,4); // to the fourth

        // mean time of phase
        $mnm = $nme + 0.0001337 * $t2 - 0.00000015 * $t3 + 0.00000000073 * $t4;

        // Sun's mean anomaly(地球绕太阳运行均值近点角)(从太阳观察)
        $m = 2.5534 + 29.10535669 * $k - 0.0000218 * $t2 - 0.00000011 * $t3;

        // Moon's mean anomaly(月球绕地球运行均值近点角)(从地球观察)
        $ms = 201.5643 + 385.81693528 * $k + 0.0107438 * $t2 + 0.00001239 * $t3 - 0.000000058 * $t4;

        // Moon's argument of latitude(月球的纬度参数)
        $f = 160.7108 + 390.67050274 * $k - 0.0016341 * $t2 - 0.00000227 * $t3 + 0.000000011 * $t4;

        // Longitude of the ascending node of the lunar orbit(月球绕日运行轨道升交点之经度)
        $omega = 124.7746 - 1.5637558 * $k + 0.0020691 * $t2 + 0.00000215 * $t3;

        // 乘式因子
        $e = 1 - 0.002516 * $t - 0.0000074 * $t2;

        $apt1 = -0.4072 * sin((M_PI / 180) * $ms);
        $apt1 += 0.17241 * $e * sin((M_PI / 180) * $m);
        $apt1 += 0.01608 * sin((M_PI / 180) * 2 * $ms);
        $apt1 += 0.01039 * sin((M_PI / 180) * 2 * $f);
        $apt1 += 0.00739 * $e * sin((M_PI / 180) * ($ms - $m));
        $apt1 -= 0.00514 * $e * sin((M_PI / 180) * ($ms + $m));
        $apt1 += 0.00208 * $e * $e * sin((M_PI / 180) * (2 * $m));
        $apt1 -= 0.00111 * sin((M_PI / 180) * ($ms - 2 * $f));
        $apt1 -= 0.00057 * sin((M_PI / 180) * ($ms + 2 * $f));
        $apt1 += 0.00056 * $e * sin((M_PI / 180) * (2 * $ms + $m));
        $apt1 -= 0.00042 * sin((M_PI / 180) * 3 * $ms);
        $apt1 += 0.00042 * $e * sin((M_PI / 180) * ($m + 2 * $f));
        $apt1 += 0.00038 * $e * sin((M_PI / 180) * ($m - 2 * $f));
        $apt1 -= 0.00024 * $e * sin((M_PI / 180) * (2 * $ms - $m));
        $apt1 -= 0.00017 * sin((M_PI / 180) * $omega);
        $apt1 -= 0.00007 * sin((M_PI / 180) * ($ms + 2 * $m));
        $apt1 += 0.00004 * sin((M_PI / 180) * (2 * $ms - 2 * $f));
        $apt1 += 0.00004 * sin((M_PI / 180) * (3 * $m));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($ms + $m - 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * (2 * $ms + 2 * $f));
        $apt1 -= 0.00003 * sin((M_PI / 180) * ($ms + $m + 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($ms - $m + 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * ($ms - $m - 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * (3 * $ms + $m));
        $apt1 += 0.00002 * sin((M_PI / 180) * (4 * $ms));

        $apt2 = 0.000325 * sin((M_PI / 180) * (299.77 + 0.107408 * $k - 0.009173 * $t2));
        $apt2 += 0.000165 * sin((M_PI / 180) * (251.88 + 0.016321 * $k));
        $apt2 += 0.000164 * sin((M_PI / 180) * (251.83 + 26.651886 * $k));
        $apt2 += 0.000126 * sin((M_PI / 180) * (349.42 + 36.412478 * $k));
        $apt2 += 0.00011 * sin((M_PI / 180) * (84.66 + 18.206239 * $k));
        $apt2 += 0.000062 * sin((M_PI / 180) * (141.74 + 53.303771 * $k));
        $apt2 += 0.00006 * sin((M_PI / 180) * (207.14 + 2.453732 * $k));
        $apt2 += 0.000056 * sin((M_PI / 180) * (154.84 + 7.30686 * $k));
        $apt2 += 0.000047 * sin((M_PI / 180) * (34.52 + 27.261239 * $k));
        $apt2 += 0.000042 * sin((M_PI / 180) * (207.19 + 0.121824 * $k));
        $apt2 += 0.00004 * sin((M_PI / 180) * (291.34 + 1.844379 * $k));
        $apt2 += 0.000037 * sin((M_PI / 180) * (161.72 + 24.198154 * $k));
        $apt2 += 0.000035 * sin((M_PI / 180) * (239.56 + 25.513099 * $k));
        $apt2 += 0.000023 * sin((M_PI / 180) * (331.55 + 3.592518 * $k));

        return $mnm + $apt1 + $apt2;
    }

    /**
     * 朔望月长jd
     * @param float $k
     *
     * @return float
     */
    public static function meanTimeOfPhase(float $k):float
    {
        $nme = self::BNM + $k * self::MSM;
        $t = Julian::julianCentury($nme);

        return $nme + 0.0001337 * pow($t,2) - 0.00000015 * pow($t,3) + 0.00000000073 * pow($t,4);
    }

    /**
     * 对于指定的日期时刻JD值jd,算出其为相对于基准点(之后或之前)的第几个朔望月
     *
     * @param float $jd
     *
     * @return int 反回相对于基准点(之后或之前)的朔望月序数
     */
    public static function referenceLunarMonthNum(float $jd):int
    {
        $k = floor(($jd - self::BNM) / self::MSM);

        return (int)$k;
    }

}