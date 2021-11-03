<?php
/**
 * # 知识点：
 *
 * ---
 *
 * ## 阴历
 *
 * 阴历是根据月亮围绕地球转动的规律制定的。月亮也叫“太阴”，所以阴历也叫太阴历。阴历的1个月叫作“朔望月”，
 * 每月初一为朔日（看不见月亮），十五为望日（满月）。“朔望月”是月相盈亏的平均周期，所谓“月有阴晴圆缺”，说的就是这个意思。
 * 阴历月份以朔望月长度29.5306天为基础，为了避免有小数，所以规定大月30天，小月29天。为保证每月的头一天（初一）必须是朔日，
 * 就使得大小月的安排不固定，而需要通过严格的观测和计算来确定。因此，阴历中连续2个月是大月或是小月的事是常有的，
 * 甚至还出现过如1990年三、四月是小月，九、十、十一、十二月连续4个月是大月的罕见特例。
 *
 * ## 农历
 *
 * 1个阴历年要比1个阳历年的天数少11天左右，3年下来就少了1个多月。为了不使时序和天时错乱，就有了“置闰法”。
 * 每19年中设置7个闰月，有闰月的年份1年383天或384天，称为闰年。经过计算，每3年中加1个闰月，每5年中加2个闰月，每7年中加3个闰月……每19年中加7个闰月。
 * 19年中加7个闰月后，误差消除得只差2小时9分多，这已经是够精确的了，阳历和农历基本同步了。
 * 所以，农历就采用了19年加7个闰月的办法，即“十九年七闰法”，把阳历年与农历年很好地协调起来，使农历的春节总保持在冬末春初，使农历年的平均长度接近阳历年。
 * 而农历中的月又有鲜明的月相特征，这样，农历就具有了保持阳历和阴历两全其美的特点。
 *
 * 农历闰月的设置与二十四节气有关。二十四节气是根据阳历制定的，阳历每个月固定有2个节气（前一个叫“节气”，后一个叫“中气”）。
 * 二十四节气在农历中的日期并不固定，是逐月推迟的，于是有的农历月份，中气落在月末，下个月就没有中气。一般每过2年多就有一个没有中气的月，
 * 这正好和需要加闰月的年头相符。所以农历就规定从“冬至”开始，当出现第一个没有中气的那个月作为闰月。例如2001年农历四月二十九日是中气小满，再隔1个月的初一才是下一个中气夏至，
 * 当中这1个月没有中气，就定为闰月，它跟在四月后面，所以叫闰四月。可见，农历闰哪个月，完全决定于一年中的二十四个节气。
 * 秦代以前，有一些时候是把闰月放在一年的末尾，叫作“十三月”。汉初将闰月放在九月后，叫作“后九月”。
 * 到了汉武帝太初元年（公元前104年）又规定不含中气的月份为前一个月的闰月，用上个月的名称，再加上一个“闰”字，直到现在仍沿用这个规定。
 *
 * ## 儒略日
 *
 * 法国学者 Joseph Justus Scaliger（1540-1609）设计了一种历法，称为“儒略日”(Julian day)，以JD表示。
 * 它是以7980年为一周期，我们所在的这一周期是公元前4713年(-4712年)1月1日12时为起点，在这周期内，每一个数就对应唯一的一天，
 * 就像是把一般的日历拉成一条直线，完全以天数来计算，如此在计算上十分方便。
 *
 * ## 儒略历、格里历(阳历)
 *
 * 儒略历是格里历的前身，格里历是在公元1582年10月15日开始使用的，在这之前使用的是儒略历(与儒略日不同，这是种历法)，
 * 儒略历是在公元前46年由罗马帝国的儒略·凯撒采纳埃及托勒密王朝亚历山大港的希腊数学家兼天文学家索西琴尼计算的历法，
 * 它只设定每隔四年的2月为闰月，所以平年为365天，闰年为366天，平均每年为365.25天，但平均回归年为365.24219天，与365.25天差了0.00781天，
 * 1500年后就差了11.7天。由于累积误差随着时间越来越大，1582年，罗马教皇格里高利十三世对儒略历进行修改，
 * 在原有的基础上规定百年不闰，四年百又闰，称为格里历，自1582年10月4日的次日开始使用，为了减少先前儒略历所造成的天数误差，10月4的次日直接跳到15日。
 *
 * > (维基百科)如今包括俄罗斯正教会在内的东欧各东方基督教社群在计算宗教节日时均仍依据传统的儒略历，除此之外现今只有苏格兰昔德兰群岛之富拉岛、阿索斯神权共和国和北非的柏柏尔人使用。
 */
declare(strict_types=1);

namespace phpu\calendar;

use Exception;
use DateTime;
use DateInterval;
use DateTimeZone;
use InvalidArgumentException;

class Date
{
    /**
     * 是否区分 早晚子时, true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
     * @var bool
     */
    public static $nightZiHour = false;

    /**
     * 均值朔望月长(mean length of synodic month)
     * @var float
     */
    private const MEAN_LENGTH_OF_SYNODIC_MONTH = 29.530588853;


    /**
     * 构造
     * Date constructor.
     */
    public function __construct(){

    }

    /**
     * 格里历日期转儒略日
     *
     * (为了简便计算，1582年10月15日之前按儒略历方式计算,
     * 也就是说在儒略历中不存在的1582年10月5日-1582年10月14日这十天的儒略日是不正确的,
     * 正确的做法是判断日期在这十天内则不计算儒略历,该方法不做判断)
     *
     * @param DateTime $dt 要转化的日期时间，以中午12点分隔
     * @param bool $mjd 是否使用简化儒略日 1858年11月17日0时开始
     *
     * @return float 儒略日
     */
    public static function gregorianToJD (DateTime $dt, bool $mjd = false):float
    {
        $YnjGis = $dt->format('Y,n,j,G,i,s');
        if(false === $YnjGis){
            $YnjGis = '1582,10,15,12,0,0';
        }
        [$Y,$M,$D,$H,$I,$S] = array_map('floatval', explode(',',$YnjGis,6));

        // 计算儒略日开始

        $a = floor((14-$M)/12); // 因计算公式需月份大于2

        $y = $Y + 4800 - $a;
        $m = $M + 12 * $a -3;
        $d = $D + $H / 24.0 + $I / 1440.0 + $S / 86400.0;
        // 公式 https://blog.csdn.net/weixin_42763614/article/details/82880007
        $jdn = (float)0;
        if(($Y<1582) || ($Y==1582 && $M<10) || ($Y==1582 && $M==10 && $D<15)){
            // 适用于儒略历日期(中午12点)
            $jdn = $d + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;
        }else{
            // 适用于格里历日期(中午12点)
            $jdn = $d + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - floor($y / 100) + floor($y / 400)-32045;

            // 在php语言中，有Calendar扩展库，该库中有GregorianToJD函数可以代替上面的公式计算用格里历的儒略日,不同之处是0时分隔
            // $jdn = GregorianToJD((int)$M,(int)$D,(int)$Y) + 0.5;
        }

        $jd = $jdn - 0.5;
        // 计算儒略日结束

        // 简化儒略日
        if($mjd && ($Y > 1858 || ($Y==1858 && $M>11) || ($Y==1858 && $M==11 && $D>=17) )){
            $jd -= 2400000.5;
        }

        return (float)$jd;
    }

    /**
     * 儒略日转格里日历日期时间
     *
     * @param float             $jd 儒略日
     * @param DateTimeZone|null $timeZone
     *
     * @return DateTime
     * @throws Exception
     */
    public static function jdToGregorian(float $jd, DateTimeZone $timeZone=null):DateTime
    {

        $jdn = $jd + 0.5;

        // 公式 https://blog.csdn.net/weixin_42763614/article/details/82880007
        $Z = floor($jdn); // 儒略日的整数部分,即为所求日到-4712年1月1日0时的日数
        $F = $jdn - $Z; // 儒略日的小数部分

        $A = (float)0;
        // 2299161 是1582年10月15日12时0分0秒
        if($Z < 2299161){
            //儒略历
            $A = $Z;
        }else{
            // 格里历
            $a = floor(($Z - 2305447.5) / 36524.25);
            $A = $Z + 10 + $a - floor($a/4);
        }

        $dayLi = (float)1;
        $E = (float)0;
        $k = 0;
        while (true){
            $B = $A + 1524; // 以BC4717年3月1日0时为历元
            $C = floor(($B - 122.1) / 365.25); // 积年
            $D = floor(365.25 * $C); // 积年的日数
            $E = floor(($B - $D) / 30.6); // B-D为年内积日，E即月数
            $dayLi = $B - $D - floor(30.6 * $E) + $F;
            if($dayLi >= 1) break; // 否则即在上一月，可前置一日重新计算
            $A -= 1;
            $k += 1;
        }

        $month = $E < 14 ? $E - 1 : $E - 13;
        $year = $month > 2 ? $C - 4716 : $C - 4715;
        $dayLi += $k;
        if(intval($dayLi,10) === 0) $dayLi += 1;

        // 天数分开成天与时分秒
        $day = floor($dayLi);
        $dayF = $dayLi - $day;
        $hh = $ii = $ss = (float)0;
        if($dayF > 0){
            $sd = $dayF * 24 * 60 * 60 + 0.00005; // 考虑精度，0.00005
            $ss = (float)($sd % 60);
            $mt = floor($sd / 60);
            $ii = (float)($mt % 60);
            $hh = floor($mt / 60);
        }

        return new DateTime(sprintf('%04.0f-%02.0f-%02.0f %02.0f:%02.0f:%02.0f', $year, $month, $day, $hh, $ii, $ss), $timeZone);
    }

    /**
     * 地球在绕日运行时会因受到其他星球之影响而产生摄动(perturbation)
     *
     * @param float $jd 儒略日
     * @return float 返回某时刻(儒略日)的摄动偏移量
     */
    public static function perturbation(float $jd):float
    {
        $ptsa = [485, 203, 199, 182, 156, 136, 77, 74, 70, 58, 52, 50, 45, 44, 29, 18, 17, 16, 14, 12, 12, 12, 9, 8];
        $ptsb = [324.96, 337.23, 342.08, 27.85, 73.14, 171.52, 222.54, 296.72, 243.58, 119.81, 297.17, 21.02, 247.54,
            325.15,60.93, 155.12, 288.79, 198.04, 199.76, 95.39, 287.11, 320.81, 227.73, 15.45];
        $ptsc = [1934.136, 32964.467, 20.186, 445267.112, 45036.886, 22518.443, 65928.934, 3034.906, 9037.513,
            33718.147, 150.678, 2281.226, 29929.562, 31555.956, 4443.417, 67555.328, 4562.452, 62894.029, 31436.921,
            14577.848, 31931.756, 34777.259, 1222.114, 16859.074];

        $t = ($jd - 2451545) / 36525;
        $s = (float)0;
        for ($k = 0; $k <= 23; $k++) {
            $s = $s + $ptsa[$k] * cos($ptsb[$k] * 2 * M_PI / 360 + $ptsc[$k] * 2 * M_PI / 360 * $t);
        }
        $w = 35999.373 * $t - 2.47;
        $l = 1 + 0.0334 * cos($w * 2 * M_PI / 360) + 0.0007 * cos(2 * $w * 2 * M_PI / 360);

        return 0.00001 * $s / $l;
    }


    /**
     * 地球自转速度调整值Delta T(以△t表示)
     * 计算介于-1999年至3000年之间的△t值
     *
     * @param float $yy // 年
     * @param float $mm // 月
     * @return float 地球自转速度调整值，单位：分
     */
    public static function deltaT(float $yy, float $mm):float {
        $y = $yy + ($mm - 0.5) / 12;

        $dt = (float)0;

        if ($y <= -500) {
            $u = ($y - 1820) / 100;
            $dt = (-20 + 32 * $u * $u);
        }else if($y < 500){
            $u = $y / 100;
            $dt = 10583.6
                - 1014.41 * $u
                + 33.78311 * pow($u,2)
                - 5.952053 * pow($u,3)
                - 0.1798452 * pow($u,4)
                + 0.022174192 * pow($u,5)
                + 0.0090316521 * pow($u,6);
        }else if($y < 1600){
            $u = ($y - 1000) / 100;
            $dt = 1574.2
                - 556.01 * $u
                + 71.23472 * pow($u,2)
                + 0.319781 * pow($u,3)
                - 0.8503463 * pow($u,4)
                - 0.005050998 * pow($u,5)
                + 0.0083572073 * pow($u,6);
        }else if($y < 1700){
            $t = $y - 1600;
            $dt = 120
                - 0.9808 * $t
                - 0.01532 * pow($t,2)
                + pow($t,3) / 7129;
        }else if($y < 1800){
            $t = $y - 1700;
            $dt = 8.83
                + 0.1603 * $t
                - 0.0059285 * pow($t,2)
                + 0.00013336 * pow($t,3)
                - pow($t,4) / 1174000;
        }else if($y < 1860){
            $t = $y - 1800;
            $dt = 13.72
                - 0.332447 * $t
                + 0.0068612 * pow($t,2)
                + 0.0041116 * pow($t,3)
                - 0.00037436 * pow($t,4)
                + 0.0000121272 * pow($t,5)
                - 0.0000001699 * pow($t,6)
                + 0.000000000875 * pow($t,7);
        }else if($y < 1900){
            $t = $y - 1860;
            $dt = 7.62
                + 0.5737 * $t
                - 0.251754 * pow($t,2)
                + 0.01680668 * pow($t,3)
                - 0.0004473624 * pow($t,4)
                + pow($t,5) / 233174;
        }else if($y < 1920){
            $t = $y - 1900;
            $dt = -2.79
                + 1.494119 * $t
                - 0.0598939 * pow($t,2)
                + 0.0061966 * pow($t,3)
                - 0.000197 * pow($t,4);
        }else if($y < 1941){
            $t = $y - 1920;
            $dt = 21.2
                + 0.84493 * $t
                - 0.0761 * pow($t,2)
                + 0.0020936 * pow($t,3);
        }else if($y < 1961){
            $t = $y - 1950;
            $dt = 29.07
                + 0.407 * $t
                - pow($t,2) / 233
                + pow($t,3) / 2547;
        }else if($y < 1986){
            $t = $y - 1975;
            $dt = 45.45
                + 1.067 * $t
                - pow($t,2) / 260
                - pow($t,3) / 718;
        }else if($y < 2005){
            $t = $y - 2000;
            $dt = 63.86
                + 0.3345 * $t
                - 0.060374 * pow($t,2)
                + 0.0017275 * pow($t,3)
                + 0.000651814 * pow($t,4)
                + 0.00002373599 * pow($t,5);
        }else if($y < 2050){
            $t = $y - 2000;
            $dt = 62.92
                + 0.32217 * $t
                + 0.005589 * pow($t,2);
        }else if($y < 2150){
            $u = ($y - 1820) / 100;
            $dt = -20
                + 32 * pow($u,2)
                - 0.5628 * (2150 - $y);
        }else {
            $u = ($y - 1820) / 100;
            $dt = -20 + 32 * pow($u,2);
        }

        if ($y < 1955 || $y >= 2005){
            $dt = $dt - (0.000012932 * ($y - 1955) * ($y - 1955));
        }
        
        return $dt / 60.0; // 将秒转换为分
    }

    /**
     * 计算指定年(格里历)的春分点(vernal equinox)
     * 两个春分点之间为一个回归年长
     *
     * @param DateTime $dt
     *
     * @return float 春分点的jd值
     * @throws Exception
     */
    public static function vernalEquinox(DateTime $dt):float {

        $yy = floatval($dt->format('Y'));

        if($yy > 8001 || $yy < -8000){
            throw new Exception('Date not allowed');
        }

        if ($yy >= 1000 && $yy <= 8001) {
            $m = ($yy - 2000) / 1000;
            return 2451623.80984 + 365242.37404 * $m + 0.05169 * pow($m,2) - 0.00411 * pow($m,3) - 0.00057 * pow($m,4);
        }else{
            $m = $yy / 1000;
            return 1721139.29189 + 365242.1374 * $m + 0.06134 * pow($m,2) + 0.00111 * pow($m,3) - 0.00071 * pow($m,4);
        }
    }

    /**
     * 获取指定年的春分开始的24节气,另外多取2个确保覆盖完一个公历年
     *
     * @param DateTime $dt
     *
     * @return array<float> 节气的儒略日
     * @throws Exception
     */
    public static function meanSolarTermsJD(DateTime $dt):array
    {
        $dtClone = clone $dt; // copy DateTime
        // 该年的春分點
        $jdve = self::vernalEquinox($dt);

        $yy = floatval($dt->format('Y'));

        $ty = self::vernalEquinox($dtClone->add(new DateInterval('P1Y'))) - $jdve; // 该年的回归年长

        $num = 24 + 2; //另外多取2个确保覆盖完一个公历年

        $ath = 2 * M_PI / 24;

        $tx = ($jdve - 2451545) / 365250;
        $e = 0.0167086342
            - 0.0004203654 * $tx
            - 0.0000126734 * pow($tx,2)
            + 0.0000001444 * pow($tx,3)
            - 0.0000000002 * pow($tx,4)
            + 0.0000000003 * pow($tx,5);

        $tt = $yy / 1000;
        $vp = 111.25586939
            - 17.0119934518333 * $tt
            - 0.044091890166673 * pow($tt,2)
            - 4.37356166661345E-04 * pow($tt,3)
            + 8.16716666602386E-06 * pow($tt,4);

        $rvp = $vp * 2 * M_PI / 360;
        $peri = [];
        for ($i = 0; $i < $num; $i++) {
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
        $jqjd = [];
        for ($i = 0; $i < $num; $i++) {
            $jqjd[$i] = $jdve + $peri[$i] - $peri[0];
        }

        return $jqjd;
    }

    /**
     * 获取指定年的春分开始作perturbation调整后的24节气,可以多取2个
     *
     * @param DateTime $dt
     * @param int      $start 开始索引(春分开始的节气索引 0-25)
     * @param int      $end   结束索引(春分开始的节气索引 0-25)
     *
     * @return float[]
     * @throws Exception
     */
    public static function adjustedSolarTerms(DateTime $dt, int $start=0, int $end=25):array
    {
        $yy = floatval($dt->format('Y'));

        if ($start < 0 || $start > 24 || $end < 1 || $end > 25) {
            throw new Exception('The number of solar terms exceeds the limit');
        };


        $jq = [];

        $jqjd = self::meanSolarTermsJD($dt); // 获取该年春分开始的24节气时间点

        foreach ($jqjd as $k => $jd){
            if($k < $start){
                continue;
            }
            if($k > $end){
                continue;
            }
            $ptb = self::perturbation($jd); // 取得受perturbation影响所需微调
            $ut = self::deltaT($yy, floor(($k+1) / 2) + 3); // 修正dynamical time to Universal time
            $jq[$k] = $jd + $ptb - $ut / 60 / 24; // 加上摄动调整值ptb,减去对应的Delta T值(分钟转换为日)
            $jq[$k] = $jq[$k] + 1 / 3; // 因中国(北京、重庆、上海)时间比格林威治时间先行8小时，即1/3日
        }

        return $jq;
    }

    /**
     * 求出以某年立春点开始的节(注意:为了方便计算起运数,此处第0位为上一年的小寒)
     *
     * @param DateTime $dt
     *
     * @return float[]
     * @throws Exception
     */
    public static function pureJQsinceSpring(DateTime $dt):array
    {
        $dtClone = clone $dt;

        $jdpjq = [];

        $dj = self::adjustedSolarTerms($dtClone->sub(new DateInterval('P1Y')), 19, 23); // 求出含指定年立春开始之3个节气JD值,以前一年的年值代入

        foreach ($dj as $k => $v){
            if($k < 19){
                continue;
            }
            if($k > 23){
                continue;
            }
            if($k % 2 == 0){
                continue;
            }
            $jdpjq[] = $dj[$k]; // 19小寒;20大寒;21立春;22雨水;23惊蛰
        }

        $dj = self::adjustedSolarTerms($dt, 0, 25); // 求出指定年节气之JD值,从春分开始,到大寒,多取两个确保覆盖一个公历年,也方便计算起运数

        foreach ($dj as $k => $v){
            if($k % 2 == 0){
                continue;
            }
            $jdpjq[] = $dj[$k];
        }

        return $jdpjq;
    }

    /**
     * 求出自冬至点为起点的连续15个中气
     *
     * @param DateTime $dt
     *
     * @return float[]
     * @throws Exception
     */
    public static function zQsinceWinterSolstice(DateTime $dt):array
    {
        $dtClone = clone $dt;

        $jdzq = [];

        $dj = self::adjustedSolarTerms($dtClone->sub(new DateInterval('P1Y')), 18, 23); // 求出指定年冬至开始之节气JD值,以前一年的值代入

        $jdzq[0] = $dj[18]; //冬至
        $jdzq[1] = $dj[20]; //大寒
        $jdzq[2] = $dj[22]; //雨水

        $dj = self::adjustedSolarTerms($dt, 0, 23); // 求出指定年节气之JD值

        foreach ($dj as $k => $v){
            if($k%2 != 0){
                continue;
            }
            $jdzq[] = $dj[$k];
        }

        return $jdzq;
    }

    /**
     * 求出实际新月点
     * 以2000年初的第一个均值新月点为0点求出的均值新月点和其朔望月之序数 k 代入此方法来求算实际新月点
     *
     * @param float $k
     * @return float
     */
    public static function trueNewMoon(float $k):float
    {
        $jdt = 2451550.09765 + $k * self::MEAN_LENGTH_OF_SYNODIC_MONTH;
        $t = ($jdt - 2451545) / 36525; // 2451545为2000年1月1日正午12时的JD
        $t2 = pow($t,2); // square for frequent use
        $t3 = pow($t,3); // cube for frequent use
        $t4 = pow($t,4); // to the fourth
        // mean time of phase
        $pt = $jdt + 0.0001337 * $t2 - 0.00000015 * $t3 + 0.00000000073 * $t4;
        // Sun's mean anomaly(地球绕太阳运行均值近点角)(从太阳观察)
        $m = 2.5534 + 29.10535669 * $k - 0.0000218 * $t2 - 0.00000011 * $t3;
        // Moon's mean anomaly(月球绕地球运行均值近点角)(从地球观察)
        $mprime = 201.5643 + 385.81693528 * $k + 0.0107438 * $t2 + 0.00001239 * $t3 - 0.000000058 * $t4;
        // Moon's argument of latitude(月球的纬度参数)
        $f = 160.7108 + 390.67050274 * $k - 0.0016341 * $t2 - 0.00000227 * $t3 + 0.000000011 * $t4;
        // Longitude of the ascending node of the lunar orbit(月球绕日运行轨道升交点之经度)
        $omega = 124.7746 - 1.5637558 * $k + 0.0020691 * $t2 + 0.00000215 * $t3;
        // 乘式因子
        $es = 1 - 0.002516 * $t - 0.0000074 * $t2;
        // 因perturbation造成的偏移：
        $apt1 = -0.4072 * sin((M_PI / 180) * $mprime);
        $apt1 += 0.17241 * $es * sin((M_PI / 180) * $m);
        $apt1 += 0.01608 * sin((M_PI / 180) * 2 * $mprime);
        $apt1 += 0.01039 * sin((M_PI / 180) * 2 * $f);
        $apt1 += 0.00739 * $es * sin((M_PI / 180) * ($mprime - $m));
        $apt1 -= 0.00514 * $es * sin((M_PI / 180) * ($mprime + $m));
        $apt1 += 0.00208 * $es * $es * sin((M_PI / 180) * (2 * $m));
        $apt1 -= 0.00111 * sin((M_PI / 180) * ($mprime - 2 * $f));
        $apt1 -= 0.00057 * sin((M_PI / 180) * ($mprime + 2 * $f));
        $apt1 += 0.00056 * $es * sin((M_PI / 180) * (2 * $mprime + $m));
        $apt1 -= 0.00042 * sin((M_PI / 180) * 3 * $mprime);
        $apt1 += 0.00042 * $es * sin((M_PI / 180) * ($m + 2 * $f));
        $apt1 += 0.00038 * $es * sin((M_PI / 180) * ($m - 2 * $f));
        $apt1 -= 0.00024 * $es * sin((M_PI / 180) * (2 * $mprime - $m));
        $apt1 -= 0.00017 * sin((M_PI / 180) * $omega);
        $apt1 -= 0.00007 * sin((M_PI / 180) * ($mprime + 2 * $m));
        $apt1 += 0.00004 * sin((M_PI / 180) * (2 * $mprime - 2 * $f));
        $apt1 += 0.00004 * sin((M_PI / 180) * (3 * $m));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($mprime + $m - 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * (2 * $mprime + 2 * $f));
        $apt1 -= 0.00003 * sin((M_PI / 180) * ($mprime + $m + 2 * $f));
        $apt1 += 0.00003 * sin((M_PI / 180) * ($mprime - $m + 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * ($mprime - $m - 2 * $f));
        $apt1 -= 0.00002 * sin((M_PI / 180) * (3 * $mprime + $m));
        $apt1 += 0.00002 * sin((M_PI / 180) * (4 * $mprime));

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
        return $pt + $apt1 + $apt2;
    }

    /**
     * 对于指定日期时刻所属的朔望月,求出其均值新月点的月序数
     *
     * @param float $jd
     * @return float[]
     */
    public static function meanNewMoon(float $jd):array
    {
        // $kn为从2000年1月6日14时20分36秒起至指定年月日之阴历月数,以synodic month为单位
        $kn = floor(($jd - 2451550.09765) / self::MEAN_LENGTH_OF_SYNODIC_MONTH); // 2451550.09765为2000年1月6日14时20分36秒之JD值.

        $jdt = 2451550.09765 + $kn * self::MEAN_LENGTH_OF_SYNODIC_MONTH;
        // Time in Julian centuries from 2000 January 0.5.
        $t = ($jdt - 2451545) / 36525; // 以100年为单位,以2000年1月1日12时为0点
        $thejd = $jdt + 0.0001337 * pow($t,2) - 0.00000015 * pow($t,3) + 0.00000000073 * pow($t,4);
        // 2451550.09765为2000年1月6日14时20分36秒,此为2000年后的第一个均值新月
        return [$kn, $thejd];
    }

    /**
     * 求算以含冬至中气为阴历11月开始的连续16个朔望月
     *
     * @param DateTime $dt
     * @param float $jdws
     * @return array
     */
    public static function sMsinceWinterSolstice(DateTime $dt, float $jdws):array
    {
        // $dtClone = clone $dt;
        $Y = $dt->format('Y');

        $tz = $dt->getTimezone();
        $ly = intval($Y,10) - 1;

        $dtlyNovember = new DateTime(strval($ly).'-11-01 00:00:00',$tz);

        $tjd = [];

        $jd = self::gregorianToJD($dtlyNovember); // 求年初前两个月附近的新月点(即前一年的11月初)

        [$kn, $thejd] = self::meanNewMoon($jd); // 求得自2000年1月起第kn个平均朔望日及期JD值
        for ($i = 0; $i <= 19; $i++) { // 求出连续20个朔望月
            $k = $kn + $i;
            // $mjd = $thejd + self::MEAN_LENGTH_OF_SYNODIC_MONTH * $i;

            $tjd[$i] = self::trueNewMoon($k) + 1 / 3; // 以k值代入求瞬时朔望日，因中国比格林威治先行8小时，加1/3天

            // 修正dynamical time to Universal time
            // 1为1月，0为前一年12月，-1为前一年11月(当i=0时，i-1代表前一年11月)
            $tjd[$i] = $tjd[$i] - self::deltaT(floatval($Y), floatval($i - 1)) / 1440;
        }

        for ($j = 0; $j <= 18; $j++) {
            if (floor($tjd[$j] + 0.5) > floor($jdws + 0.5)) {
                break;
            } // 已超过冬至中气(比较日期法)
        }

        $jdnm = [];
        for ($k = 0; $k <= 15; $k++) { // 取上一步的索引值
            $jdnm[$k] = $tjd[$j - 1 + $k]; // 重排索引,使含冬至朔望月的索引为0
        }

        return $jdnm;
    }

    /**
     * 以比较日期法求算冬月及其余各月名称代码,包含闰月,冬月为0,腊月为1,正月为2,其余类推.闰月多加0.5
     *
     * @param DateTime $dt 日期，该方法只用到了年数
     *
     * @return array<array<float>,array<float>,array<float>> [以前一年冬至为起点之连续15个中气,以含冬至中气为阴历11月(冬月)开始的连续16个朔望月的新月点,月名称代码]
     * @throws Exception
     */
    public static function zQandSMandLunarMonthCode(DateTime $dt):array
    {
        $mc = [];

        $jdzq = self::zQsinceWinterSolstice($dt); // 取得以前一年冬至为起点之连续15个中气

        $jdnm = self::sMsinceWinterSolstice($dt, $jdzq[0]); // 求出以含冬至中气为阴历11月(冬月)开始的连续16个朔望月的新月点

        $yz = 0; // 设定旗标,0表示未遇到闰月,1表示已遇到闰月
        if (floor($jdzq[12] + 0.5) >= floor($jdnm[13] + 0.5)) { // 若第13个中气jdzq(12)大于或等于第14个新月jdnm(13)
            for ($i = 1; $i <= 14; $i++) { // 表示此两个冬至之间的11个中气要放到12个朔望月中,
                // 至少有一个朔望月不含中气,第一个不含中气的月即为闰月
                // 若阴历腊月起始日大於冬至中气日,且阴历正月起始日小于或等于大寒中气日,则此月为闰月,其余同理
                if (($jdnm[$i] + 0.5) > floor($jdzq[$i - 1 - $yz] + 0.5)
                    && floor($jdnm[$i + 1] + 0.5) <= floor($jdzq[$i - $yz] + 0.5)) {
                    $mc[$i] = $i - 0.5;
                    $yz = 1; // 标示遇到闰月
                } else {
                    $mc[$i] = $i - $yz; // 遇到闰月开始,每个月号要减1
                }
            }
        } else { // 否则表示两个连续冬至之间只有11个整月,故无闰月
            for ($i = 0; $i <= 12; $i++) { // 直接赋予这12个月月代码
                $mc[$i] = $i;
            }
            for ($i = 13; $i <= 14; $i++) { // 处理次一置月年的11月与12月,亦有可能含闰月
                // 若次一阴历腊月起始日大于附近的冬至中气日,且农历正月起始日小于或等于大寒中气日,则此月为腊月,次一正月同理.
                if (($jdnm[$i] + 0.5) > floor($jdzq[$i - 1 - $yz] + 0.5)
                    && floor($jdnm[$i + 1] + 0.5) <= floor($jdzq[$i - $yz] + 0.5)) {
                    $mc[$i] = $i - 0.5;
                    $yz = 1; // 标示遇到闰月
                } else {
                    $mc[$i] = $i - $yz; // 遇到闰月开始,每个月号要减1
                }
            }
        }
        return [$jdzq, $jdnm, $mc];
    }

    /**
     * 从农历的月代码$mc中找出闰月
     * @param $mc
     * @return int 0对应农历11月,1对应农历12月,2对应农历隔年1月,依此类推
     */
    private static function getLeap($mc):int{
        $leap = 0; // 若闰月旗标为0代表无闰月
        for ($j = 1; $j <= 14; $j++) { // 确认指定年前一年11月开始各月是否闰月
            if ($mc[$j] - floor($mc[$j]) > 0) { // 若是,则将此闰月代码放入闰月旗标内
                $leap = intval(floor($mc[$j] + 0.5)); // leap = 0对应农历11月,1对应农历12月,2对应农历隔年1月,依此类推.
                break;
            }
        }
        return $leap;
    }

    /**
     * 获取农历某年的闰月,0为无闰月
     *
     * @param float       $yy       农历年
     * @param string|null $timeZone 时区
     *
     * @return int 闰几月，返回值是几就闰几月，0为无闰月
     * @throws Exception
     */
    public static function leap(float $yy, string $timeZone=null):int
    {
        $dt = new DateTime(strval($yy).'-01-01 12:00:00', $timeZone); // 实际使用格里历的年

        [, , $mc] = self::zQandSMandLunarMonthCode($dt);

        $leap = self::getLeap($mc);

        return (int)max(0, $leap-2);
    }

    /**
     * 获取农历某个月有多少天
     *
     * @param float       $yy       农历年数字
     * @param float       $mm       农历月数字
     * @param int         $isLeap   是否是闰月
     * @param string|null $timeZone 时区
     *
     * @return int 农历某个月天数
     * @throws Exception
     */
    public static function lunarDays(float $yy, float $mm, int $isLeap=0, string $timeZone=null):int
    {
        if ($yy < -1000 || $yy > 3000) { // 适用于公元-1000至公元3000,超出此范围误差较大
            throw new Exception('Date not allowed');
        }
        if ($mm < 1 || $mm > 12){ // 月份须在1-12月之内
            throw new Exception('Date not allowed');
        }

        $dt = new DateTime(strval($yy).'-01-01 12:00:00', $timeZone); // 实际使用格里历的年

        [, $jdnm, $mc] = self::zQandSMandLunarMonthCode($dt);

        $leap = self::getLeap($mc);

        // 11月对应到1,12月对应到2,1月对应到3,2月对应到4,依此类推
        $mm = $mm + 2;

        // 求算农历各月之大小,大月30天,小月29天
        for ($i = 0; $i <= 14; $i++) {
            $nofd[$i] = floor($jdnm[$i + 1] + 0.5) - floor($jdnm[$i] + 0.5); // 每月天数,加0.5是因JD以正午起算
        }

        $dy = 0; // 当月天数

        if ($isLeap){ // 闰月
            if ($leap < 3) { // 而旗标非闰月或非本年闰月,则表示此年不含闰月.leap=0代表无闰月,=1代表闰月为前一年的11月,=2代表闰月为前一年的12月
                throw new InvalidArgumentException(sprintf('isLeap value "%d" is invalid.', (int)$leap)); // 该年不是闰年
            } else { // 若本年內有闰月
                if ($leap != $mm) { // 但不为指定的月份
                    throw new InvalidArgumentException(sprintf('the month value "%d" is invalid.', (int)$mm)); // 该月非该年的闰月，此月不是闰月
                } else { // 若指定的月份即为闰月
                    $dy = $nofd[$mm];
                }
            }
        } else { // 若没有指明是闰月
            if ($leap == 0) { // 若旗标非闰月,则表示此年不含闰月(包括前一年的11月起之月份)
                $dy = $nofd[$mm - 1];
            } else { // 若旗标为本年有闰月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意为:若指定月大于闰月,则索引用mx,否则索引用mx-1
                $dy = $nofd[$mm + ($mm > $leap) - 1];
            }
        }

        return (int)$dy;
    }


    /**
     * 农历转格里历
     *
     * @param float       $yy       农历年
     * @param float       $mm       农历月
     * @param float       $dd       农历日
     * @param int         $isLeap   指定的月是否是闰月
     * @param DateTimeZone|null $timeZone 时区
     *
     * @return DateTime
     * @throws Exception
     */
    public static function lunarToGregorian(float $yy, float $mm, float $dd, int $isLeap=0, DateTimeZone $timeZone = null):DateTime
    {
        if ($yy < -1000 || $yy > 3000) { //适用于公元-1000年至公元3000年,超出此范围误差较大
            throw new Exception('Date not allowed');
        }
        if ($mm < 1 || $mm > 12){ //输入月份必须在1-12月之內
            throw new Exception('Date not allowed');
        }
        if ($dd < 1 || $dd > 30) { //输入日期必须在1-30日之內
            throw new Exception('Date not allowed');
        }

        $dt = new DateTime(strval($yy).'-01-01 12:00:00', $timeZone); // 实际使用格里历的年

        [, $jdnm, $mc] = self::zQandSMandLunarMonthCode($dt);

        $leap = self::getLeap($mc);

        // 11月对应到1,12月对应到2,1月对应到3,2月对应到4,依此类推
        $mm = $mm + 2;

        // 求算农历各月之大小,大月30天,小月29天
        for ($i = 0; $i <= 14; $i++) {
            $nofd[$i] = floor($jdnm[$i + 1] + 0.5) - floor($jdnm[$i] + 0.5); // 每月天数,加0.5是因JD以正午起算
        }

        $jd = 0; // 儒略日时间
        $er = 0; // 若参数有错误，er值将被设为非0值

        if ($isLeap){ // 闰月
            if ($leap < 3) { // 而旗标非闰月或非本年闰月,则表示此年不含闰月.leap=0代表无闰月,=1代表闰月为前一年的11月,=2代表闰月为前一年的12月
                $er = 1; // 该年非闰年
            } else { // 若本年內有闰月
                if ($leap != $mm) { // 但不为指定的月份
                    $er = 2; // 该月非该年的闰月，此月不是闰月
                } else { // 若指定的月份即为闰月
                    if ($dd <= $nofd[$mm]) { // 若日期不大于当月天数
                        $jd = $jdnm[$mm] + $dd - 1; // 则将当月之前的JD值加上日期之前的天数
                    } else { // 日期超出范围
                        $er = 3;
                    }
                }
            }
        } else { // 若没有指明是闰月
            if ($leap == 0) { // 若旗标非闰月,则表示此年不含闰月(包括前一年的11月起之月份)
                if ($dd <= $nofd[$mm - 1]) { // 若日期不大于当月天数
                    $jd = $jdnm[$mm - 1] + $dd - 1; // 则将当月之前的JD值加上日期之前的天数
                } else { // 日期超出范围
                    $er = 4;
                }
            } else { // 若旗标为本年有闰月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意为:若指定月大于闰月,则索引用mx,否则索引用mx-1
                if ($dd <= $nofd[$mm + ($mm > $leap) - 1]) { // 若日期不大于当月天数
                    $jd = $jdnm[$mm + ($mm > $leap) - 1] + $dd - 1; // 则将当月之前的JD值加上日期之前的天数
                } else { // 日期超出范围
                    $er = 4;
                }
            }
        }

        // 去掉时分秒
        $jd = floor($jd) + 0.5;

        if(!$er){
            return self::jdToGregorian($jd,$timeZone);
        }else{
            throw new InvalidArgumentException('An argument does not match with the expected value');
        }
    }

    /**
     * 格里历日期转农历日期
     *
     * @param DateTime $dt
     *
     * @return array
     * @throws Exception
     */
    public static function gregorianToLunar(DateTime $dt):array {

        //$dtClone = clone $dt;

        $yy = floatval($dt->format('Y'));


        $prev = 0; // 是否跨年了,跨年了则减一
        $isLeap = (float)0;// 是否闰月

        [, $jdnm, $mc] = self::zQandSMandLunarMonthCode($dt);

        $jd = self::gregorianToJD($dt); // 求出指定年月日之JD值
        $jda = $jd + 0.5; // 加0.5是将起始点从正午改为0时开始

        if (floor($jda) < floor($jdnm[0] + 0.5)) {
            $prev = 1;
            $dtClone2 = clone $dt;
            $dtClone2->sub(new DateInterval('P1Y'));
            [, $jdnm, $mc] = self::zQandSMandLunarMonthCode($dtClone2);
        }
        for ($i = 0; $i <= 14; $i++) { // 指令中加0.5是为了改为从0时算起而不是从中午算起
            if (floor($jda) >= floor($jdnm[$i] + 0.5) && floor($jda) < floor($jdnm[$i + 1] + 0.5)) {
                $mi = $i;
                break;
            }
        }

        if ($mc[$mi] < 2 || $prev == 1) { // 年
            $yy -= 1;
        }

        if (($mc[$mi] - floor($mc[$mi])) * 2 + 1 != 1) { // 因mc(mi)=0对应到前一年农历11月,mc(mi)=1对应到前一年农历12月,mc(mi)=2对应到本年1月,依此类推
            $isLeap = 1;
        }
        $mm = (float)(floor($mc[$mi] + 10) % 12) + 1; // 月

        $dd = floor($jda) - floor($jdnm[$mi] + 0.5) + 1; // 日,此处加1是因为每月初一从1开始而非从0开始

        return ['y'=>$yy,'m'=>$mm,'d'=>$dd,'leap'=>$isLeap];
    }

    /**
     * 从前一年的冬至开始到当前年的小寒共26个节气对应的格里历日期时间
     * 目的是取出当前格里历一年完整的节气
     *
     * @param DateTime $dt
     *
     * @return array
     * @throws Exception
     */
    public static function solarTerms(DateTime $dt):array
    {
        $dtClone = clone $dt;

        $jq = [];

        $dj = self::adjustedSolarTerms($dtClone->sub(new DateInterval('P1Y')), 18, 23);

        $jqnum = 0;
        foreach ($dj as $k => $v){
            if($k < 18){
                continue;
            }
            if($k > 23){
                continue;
            }

            $jqdata['i'] = ($jqnum + 18) % 24;
            $jqnum ++;

            $jqDt = self::jdToGregorian($dj[$k]);
            [$y,$m,$d,$h,$i,$s] = explode(',',$jqDt->format('Y,n,j,G,i,s'),6);
            $jqdata['d'] = [
                'y' => (int)$y,
                'm' => (int)$m,
                'd' => (int)$d,
                'h' => (int)$h,
                'i' => (int)$i,
                's' => (int)$s
            ];
            $jqak = $y.'-'.$m.'-'.$d;
            $jq[$jqak] = $jqdata;
        }

        $dj = self::adjustedSolarTerms($dt, 0, 19);

        foreach ($dj as $k => $v){
            $jqdata['i'] = ($jqnum + 18) % 24;
            $jqnum ++;

            $jqDt = self::jdToGregorian($dj[$k]);
            [$y,$m,$d,$h,$i,$s] = explode(',',$jqDt->format('Y,n,j,G,i,s'),6);
            $jqdata['d'] = [
                'y' => (int)$y,
                'm' => (int)$m,
                'd' => (int)$d,
                'h' => (int)$h,
                'i' => (int)$i,
                's' => (int)$s
            ];
            $jqak = $y.'-'.$m.'-'.$d;
            $jq[$jqak] = $jqdata;
        }

        return $jq;

    }


    /**
     * 四柱计算
     *
     * @param DateTime $dt
     *
     * @return array
     * @throws Exception
     */
    public static function sexagenaryCycle(DateTime $dt):array
    {
        $dtClone = clone $dt;
        $dtNew = clone $dt;

        $YnjGis = $dt->format('Y,n,j,G,i,s');
        if(false === $YnjGis){
            $YnjGis = '1582,10,15,12,0,0';
        }
        [$yy,$m,$d,$hh,$i,$s] = array_map(function($v){return intval($v,10);}, explode(',',$YnjGis,6));

        $jd = self::gregorianToJD($dtClone->add(new DateInterval('PT1S')));

        $gz = [];

        $jq = self::pureJQsinceSpring($dt); // 取得自立春开始的节(不包含中气)，该数组长度固定为16

        if ($jd < $jq[1]) { // jq[1]为立春，约在2月5日前后。
            $yy = $yy - 1; // 若小于jq[1]则属于前一个节气年
            $jq = self::pureJQsinceSpring($dtClone->sub(new DateInterval('P1Y'))); // 取得自立春开始的节(不包含中气)，该数组长度固定为16
        }

        $ygz = (($yy + 4712 + 24) % 60 + 60) % 60;
        $gz['y']['g'] = $ygz % 10; //年干
        $gz['y']['z'] = $ygz % 12; //年支

        for ($j = 0; $j <= 15; $j++) { // 比较求算节气月，求出月干支
            if ($jq[$j] >= $jd) { // 已超过指定时刻，故应取前一个节气
                $ix = $j-1;
                break;
            }
        }

        $tmm = (($yy + 4712) * 12 + ($ix - 1) + 60) % 60; // 数组0为前一年的小寒所以这里再减一
        $mgz = ($tmm + 50) % 60;
        $gz['m']['g'] = $mgz % 10; // 月干
        $gz['m']['z'] = $mgz % 12; // 月支

        $jda = $jd + 0.5; // 计算日柱的干支，加0.5是将起始点从正午改为0时开始
        $thes = (($jda - floor($jda)) * 86400) + 3600; // 将jd的小数部分化为秒，并加上起始点前移的一小时(3600秒)
        $dayjd = floor($jda) + $thes / 86400; // 将秒数化为日数，加回到jd的整数部分
        $dgz = (floor($dayjd + 49) % 60 + 60) % 60;
        $gz['d']['g'] = $dgz % 10; // 日干
        $gz['d']['z'] = $dgz % 12; // 日支
        if(self::$nightZiHour && ($hh >= 23)){ // 区分早晚子时,日柱前移一柱
            $gz['d']['g'] = ($gz['d']['g'] + 10 - 1) % 10;
            $gz['d']['z'] = ($gz['d']['z'] + 12 - 1) % 12;
        }

        $dh = $dayjd * 12; // 计算时柱的干支
        $hgz = (floor($dh + 48) % 60 + 60) % 60;
        $gz['h']['g'] = $hgz % 10; // 时干
        $gz['h']['z'] = $hgz % 12; // 时支

        return $gz;
    }

    /**
     * 星座索引
     * @param DateTime $dt
     * @return int
     */
    public static function zodiac(DateTime $dt):int
    {
        $nj = $dt->format('n,j'); // 星座只要知道月和日就行了

        [$mm,$dd] = array_map(function($v){return intval($v,10);}, explode(',',$nj,2));

        $dds = [20,19,21,20,21,22,23,23,23,24,22,22]; //星座的起始日期

        $kn = $mm - 1; //下标从0开始

        if ($dd < $dds[$kn]){ //如果早于该星座起始日期,则往前一个
            $kn = (($kn + 12) - 1) % 12; //确保是正数
        }

        return (int)$kn;
    }

}